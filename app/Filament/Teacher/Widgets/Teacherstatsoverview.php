<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Exam;
use App\Models\ExamSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $teacherId = Auth::id();

        $totalCourses     = Course::where('teacher_id', $teacherId)->count();
        $publishedCourses = Course::where('teacher_id', $teacherId)
            ->where('status', 'published')->count();
        $draftCourses     = $totalCourses - $publishedCourses;

        $totalStudents = CourseEnrollment::whereHas('course', fn ($q) =>
            $q->where('teacher_id', $teacherId)
        )->distinct('user_id')->count('user_id');

        $activeStudents = CourseEnrollment::whereHas('course', fn ($q) =>
            $q->where('teacher_id', $teacherId)
        )->where('status', 'active')->count();

        $totalExams = Exam::whereHas('course', fn ($q) =>
            $q->where('teacher_id', $teacherId)
        )->count();

        $totalSessions = ExamSession::whereHas('exam.course', fn ($q) =>
            $q->where('teacher_id', $teacherId)
        )->where('status', 'graded')->count();

        $passedSessions = ExamSession::whereHas('exam.course', fn ($q) =>
            $q->where('teacher_id', $teacherId)
        )->where('is_passed', true)->count();

        $passRate = $totalSessions > 0
            ? round($passedSessions / $totalSessions * 100)
            : 0;

        return [
            Stat::make('Kursus Saya', "{$publishedCourses} / {$totalCourses}")
                ->description("{$draftCourses} masih draft")
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Total Siswa', number_format($totalStudents))
                ->description("{$activeStudents} sedang aktif belajar")
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Total Ujian', $totalExams)
                ->description('Di semua kursus saya')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make('Tingkat Kelulusan', "{$passRate}%")
                ->description("{$passedSessions} dari {$totalSessions} peserta lulus")
                ->icon('heroicon-o-trophy')
                ->color($passRate >= 70 ? 'success' : 'danger'),
        ];
    }
}