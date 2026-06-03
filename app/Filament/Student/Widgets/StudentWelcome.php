<?php

namespace App\Filament\Student\Widgets;

use App\Filament\Student\Pages\MyCourses;
use App\Filament\Student\Pages\MyExam;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class StudentWelcome extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.student.widgets.student-welcome';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'greeting' => $this->getGreeting(),
            'studentName' => Auth::user()?->name ?? 'Student',
            'todayLabel' => now()->translatedFormat('d M Y'),
            'coursesUrl' => MyCourses::getUrl(),
            'examUrl' => MyExam::getUrl(),
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
