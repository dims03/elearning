<?php

namespace App\Filament\Teacher\Pages;

use App\Exports\CourseReportExport;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExamSession;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class CourseReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartBar;
    protected static ?string $navigationLabel = 'Report Nilai Siswa';
    protected static string | UnitEnum | null $navigationGroup = 'Report';
    protected static ?string $title = 'Report Nilai Siswa';
    protected string $view = 'filament.teacher.pages.course-report';
    protected static ?int $navigationSort = 1;

    public int $enrollmentPage = 1;
    public int $perPage = 15;

    public array $expandedExams = [];

    public ?int $selectedCourseId = null;

    public function getCourses()
    {
        return Course::with('teacher')
            ->where('status', 'published')
            ->orderBy('title')
            ->get();
    }
    public function toggleExam(int $examId): void
    {
        if (in_array($examId, $this->expandedExams)) {
            $this->expandedExams = array_filter($this->expandedExams, fn($id) => $id !== $examId);
        } else {
            $this->expandedExams[] = $examId;
        }
    }

    public function updatedSelectedCourseId(): void
    {
        $this->enrollmentPage = 1;
        $this->expandedExams  = [];
    }

    public function getReportData(): array
    {
        if (! $this->selectedCourseId) {
            return [];
        }

        $course = Course::with([
            'enrollments.user',
            'exams' => fn($q) => $q->withCount('sessions'),
        ])->findOrFail($this->selectedCourseId);

        $enrollments = CourseEnrollment::with(['user'])
            ->where('course_id', $this->selectedCourseId)
            ->orderBy('enrolled_at')
            ->paginate($this->perPage, ['*'], 'enrollmentPage', $this->enrollmentPage);

        $examSessions = ExamSession::with(['user', 'exam'])
            ->whereHas('exam', fn($q) => $q->where('course_id', $this->selectedCourseId))
            ->where('status', 'graded')
            ->get()
            ->groupBy('user_id');

        // Summary stats
        $totalStudents    = $enrollments->count();
        $completedStudents = $enrollments->where('status', 'completed')->count();
        $avgProgress      = $enrollments->avg('progress_percent');

        $allSessions = ExamSession::whereHas(
            'exam',
            fn($q) =>
            $q->where('course_id', $this->selectedCourseId)
        )->where('status', 'graded');

        $totalExamTakers = (clone $allSessions)->distinct('user_id')->count('user_id');
        $passedStudents  = (clone $allSessions)->where('is_passed', true)->distinct('user_id')->count('user_id');
        $avgScore        = (clone $allSessions)->avg('score');

        return [
            'course'           => $course,
            'enrollments'      => $enrollments,
            'examSessions'     => $examSessions,
            'totalStudents'    => $totalStudents,
            'completedStudents' => $completedStudents,
            'avgProgress'      => round($avgProgress ?? 0),
            'totalExamTakers'  => $totalExamTakers,
            'passedStudents'   => $passedStudents,
            'avgScore'         => round($avgScore ?? 0),
            'passRate'         => $totalExamTakers > 0
                ? round($passedStudents / $totalExamTakers * 100)
                : 0,
        ];
    }

    // Export ke Excel
    public function exportExcel(): void
    {
        if (! $this->selectedCourseId) {
            Notification::make()
                ->title('Pilih kursus terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $course   = Course::findOrFail($this->selectedCourseId);
        $filename = 'laporan-' . \Str::slug($course->title) . '-' . now()->format('Ymd') . '.xlsx';

        // Redirect ke download
        redirect()->route('teacher.export.course', ['courseId' => $this->selectedCourseId]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn() => $this->selectedCourseId !== null)
                ->url(
                    fn() => $this->selectedCourseId
                        ? route('teacher.export.course', ['courseId' => $this->selectedCourseId])
                        : '#'
                )
                ->openUrlInNewTab(),
        ];
    }
}
