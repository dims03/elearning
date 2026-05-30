<?php

namespace App\Filament\Student\Widgets;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\ExamSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalCourses    = CourseEnrollment::where('user_id', $userId)->count();
        $activeCourses   = CourseEnrollment::where('user_id', $userId)
            ->where('status', 'active')->count();
        $completedCourses = CourseEnrollment::where('user_id', $userId)
            ->where('status', 'completed')->count();

        $totalExams  = ExamSession::where('user_id', $userId)
            ->where('status', 'graded')->count();
        $passedExams = ExamSession::where('user_id', $userId)
            ->where('is_passed', true)->count();

        $avgScore = ExamSession::where('user_id', $userId)
            ->where('status', 'graded')
            ->avg('score');

        // $totalCerts = Certificate::where('user_id', $userId)->count();

        return [
            Stat::make('Kursus Diikuti', $totalCourses)
                ->description("{$activeCourses} aktif · {$completedCourses} selesai")
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Ujian Dikerjakan', $totalExams)
                ->description("{$passedExams} lulus dari {$totalExams} ujian")
                ->icon('heroicon-o-clipboard-document-list')
                ->color($passedExams === $totalExams && $totalExams > 0 ? 'success' : 'warning'),

            Stat::make('Rata-rata Nilai', $totalExams > 0 ? round($avgScore) . '%' : '—')
                ->description($totalExams > 0 ? 'Dari semua ujian yang dikerjakan' : 'Belum ada ujian')
                ->icon('heroicon-o-chart-bar')
                ->color(($avgScore ?? 0) >= 70 ? 'success' : 'danger'),

            // Stat::make('Sertifikat', $totalCerts)
            //     ->description($totalCerts > 0 ? 'Selamat! Terus semangat belajar 🎉' : 'Selesaikan kursus untuk dapat sertifikat')
            //     ->icon('heroicon-o-trophy')
            //     ->color($totalCerts > 0 ? 'success' : 'gray'),
        ];
    }
}