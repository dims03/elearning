<?php

namespace App\Filament\Student\Pages;

use App\Models\Course;
use App\Models\CourseEnrollment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyCourses extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;
    protected static ?string $navigationLabel = 'My Course';
    protected static ?string $title = 'My Course';
    protected string $view = 'filament.student.pages.my-courses';
    protected static ?int $navigationSort = 1;

    public function getViewData(): array
    {
        $userId = Auth::id();

        $enrollments = CourseEnrollment::with([
            'course.teacher',
            'course.category',
            'course.chapters.lessons',
        ])
            ->where('user_id', $userId)
            ->latest('enrolled_at')
            ->get();

        $availableCourses = $this->getAvailableCoursesQuery()
            ->with(['teacher', 'category'])
            ->withCount('enrollments')
            ->latest()
            ->get();

        return [
            'enrollments'      => $enrollments,
            'availableCourses' => $availableCourses,
        ];
    }

    public function enrollAction(): Action
    {
        $availableCoursesCount = fn (): int => $this->getAvailableCoursesQuery()->count();

        return Action::make('enroll')
            ->label('Enroll Course')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->size(Size::Large)
            ->tooltip(fn (): string => $availableCoursesCount() > 0
                ? 'Cari kursus baru yang belum kamu ikuti'
                : 'Semua kursus yang tersedia sudah kamu ikuti')
            ->extraAttributes(['class' => 'student-enroll-action'])
            ->disabled(fn (): bool => $availableCoursesCount() === 0)
            ->slideOver()
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalIcon('heroicon-o-sparkles')
            ->modalHeading('Pilih kursus berikutnya')
            ->modalDescription(fn (): string => $availableCoursesCount() > 0
                ? "Ada {$availableCoursesCount()} kursus yang siap kamu ikuti."
                : 'Belum ada kursus lain yang bisa kamu enroll saat ini.')
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Enroll Sekarang')
            ->modalCancelActionLabel('Nanti Dulu')
            ->form([
                Forms\Components\Select::make('course_id')
                    ->label('Pilih Kursus')
                    ->options(function () {
                        return $this->getAvailableCoursesQuery()
                            ->with('teacher:id,name')
                            ->orderBy('title')
                            ->get()
                            ->mapWithKeys(fn ($c) =>
                                [$c->id => $c->title . ' — ' . ($c->teacher->name ?? '')]
                            );
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Cari kursus...')
                    ->helperText('Pilih satu kursus untuk langsung masuk ke daftar belajar kamu.'),
            ])
            ->action(function (array $data) {
                $already = CourseEnrollment::where('user_id', Auth::id())
                    ->where('course_id', $data['course_id'])
                    ->exists();

                if ($already) {
                    Notification::make()
                        ->title('Kamu sudah terdaftar di kursus ini.')
                        ->warning()
                        ->send();
                    return;
                }

                CourseEnrollment::create([
                    'user_id'     => Auth::id(),
                    'course_id'   => $data['course_id'],
                    'status'      => 'active',
                    'enrolled_at' => now(),
                ]);

                Notification::make()
                    ->title('Berhasil enroll kursus! Selamat belajar 🎉')
                    ->success()
                    ->send();
            });
    }

    protected function getAvailableCoursesQuery(): Builder
    {
        return Course::query()
            ->where('status', 'published')
            ->whereNotIn(
                'id',
                CourseEnrollment::query()
                    ->where('user_id', Auth::id())
                    ->select('course_id'),
            );
    }

    protected function getHeaderActions(): array
    {
        return [$this->enrollAction()];
    }
}
