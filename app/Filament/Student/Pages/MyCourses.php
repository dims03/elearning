<?php

namespace App\Filament\Student\Pages;

use App\Models\Course;
use App\Models\CourseEnrollment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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

        $availableCourses = Course::with(['teacher', 'category'])
            ->where('status', 'published')
            ->whereNotIn('id', $enrollments->pluck('course_id'))
            ->latest()
            ->get();

        return [
            'enrollments'      => $enrollments,
            'availableCourses' => $availableCourses,
        ];
    }

    public function enrollAction(): Action
    {
        return Action::make('enroll')
            ->label('Enroll Course')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->modalHeading('Pilih Kursus untuk Diikuti')
            ->modalWidth('2xl')
            ->form([
                Forms\Components\Select::make('course_id')
                    ->label('Pilih Kursus')
                    ->options(function () {
                        $enrolled = CourseEnrollment::where('user_id', Auth::id())
                            ->pluck('course_id');

                        return Course::where('status', 'published')
                            ->whereNotIn('id', $enrolled)
                            ->get()
                            ->mapWithKeys(fn ($c) =>
                                [$c->id => $c->title . ' — ' . ($c->teacher->name ?? '')]
                            );
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Cari kursus...'),
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

    protected function getHeaderActions(): array
    {
        return [$this->enrollAction()];
    }
}
