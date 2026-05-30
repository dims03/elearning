<?php

namespace App\Filament\Student\Pages;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Learn extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;
    protected static ?string $title = 'Learning Class';
    protected static bool $shouldRegisterNavigation = false;
    protected  string $view = 'filament.student.pages.learn';

    public int $courseId = 0;
    public ?int $lessonId = null;

    // Data aktif
    public ?Course $currentCourse = null;
    public ?Lesson $currentLesson = null;

    public function mount(): void
    {
        $course = request()->integer('course');
        $this->courseId = $course;

        if (! $course) {
            redirect()->to(MyCourses::getUrl());
            return;
        }

        $enrolled = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course)
            ->exists();

        if (! $enrolled) {
            Notification::make()
                ->title('Kamu belum terdaftar di kursus ini.')
                ->warning()
                ->send();

            redirect()->to(MyCourses::getUrl());
        }

        $this->currentCourse = Course::with([
            'chapters.lessons',
            'teacher',
        ])->findOrFail($course);

        $firstLesson = $this->currentCourse->chapters
            ->sortBy('order')
            ->first()
            ?->lessons
            ->sortBy('order')
            ->first();

        $this->lessonId = $this->lessonId ?? $firstLesson?->id;

        if ($this->lessonId) {
            $this->loadLesson($this->lessonId);
        }
    }

    public function loadLesson(int $lessonId): void
    {
        $this->lessonId     = $lessonId;
        $this->currentLesson = Lesson::findOrFail($lessonId);

        LessonProgress::firstOrCreate([
            'user_id'   => Auth::id(),
            'lesson_id' => $lessonId,
        ]);

        $this->updateCourseProgress();
    }

    public function markComplete(int $lessonId): void
    {
        $progress = LessonProgress::firstOrCreate([
            'user_id'   => Auth::id(),
            'lesson_id' => $lessonId,
        ]);

        if (! $progress->is_completed) {
            $progress->markCompleted();

            Notification::make()
                ->title('Materi selesai! ✓')
                ->success()
                ->send();
        }

        $this->updateCourseProgress();
        $this->nextLesson();
    }

    public function nextLesson(): void
    {
        if (! $this->currentCourse || ! $this->lessonId) return;

        $allLessons = $this->currentCourse->chapters
            ->sortBy('order')
            ->flatMap(fn($c) => $c->lessons->sortBy('order'))
            ->values();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $this->lessonId);

        if ($currentIndex !== false && $currentIndex < $allLessons->count() - 1) {
            $this->loadLesson($allLessons[$currentIndex + 1]->id);
        } else {
            Notification::make()
                ->title('Kamu sudah menyelesaikan semua materi! 🎉')
                ->success()
                ->send();
        }
    }

    private function updateCourseProgress(): void
    {
        $totalLessons = $this->currentCourse->chapters
            ->sum(fn($c) => $c->lessons->count());

        if ($totalLessons === 0) return;

        $completedLessons = LessonProgress::where('user_id', Auth::id())
            ->whereIn(
                'lesson_id',
                $this->currentCourse->chapters
                    ->flatMap(fn($c) => $c->lessons->pluck('id'))
            )
            ->where('is_completed', true)
            ->count();

        $percent = (int) round(($completedLessons / $totalLessons) * 100);

        CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $this->courseId)
            ->update([
                'progress_percent' => $percent,
                'status'           => $percent >= 100 ? 'completed' : 'active',
                'completed_at'     => $percent >= 100 ? now() : null,
            ]);
    }

    public function getCompletedLessonIds(): array
    {
        if (! $this->currentCourse) return [];

        return LessonProgress::where('user_id', Auth::id())
            ->whereIn(
                'lesson_id',
                $this->currentCourse->chapters
                    ->flatMap(fn($c) => $c->lessons->pluck('id'))
            )
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();
    }

    public function getViewData(): array
    {
        return [
            'course'           => $this->currentCourse,
            'currentLesson'    => $this->currentLesson,
            'completedIds'     => $this->getCompletedLessonIds(),
            'enrollment'       => CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $this->courseId)
                ->first(),
        ];
    }
}
