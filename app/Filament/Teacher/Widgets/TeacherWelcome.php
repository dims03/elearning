<?php

namespace App\Filament\Teacher\Widgets;

use App\Filament\Teacher\Pages\CourseReport;
use App\Filament\Teacher\Resources\Courses\CourseResource;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TeacherWelcome extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.teacher.widgets.teacher-welcome';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'greeting' => $this->getGreeting(),
            'teacherName' => Auth::user()?->name ?? 'Teacher',
            'todayLabel' => now()->translatedFormat('d M Y'),
            'coursesUrl' => CourseResource::getUrl(),
            'reportUrl' => CourseReport::getUrl(),
        ];
    }

    protected function getGreeting(): string
    {
        return match (true) {
            now()->hour < 11 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}
