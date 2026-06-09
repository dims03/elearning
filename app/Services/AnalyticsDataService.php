<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsDataService
{
    /**
     * Ambil semua data analytics yang relevan untuk dikirim ke Gemini.
     * Data ini dijadikan "context" agar Gemini bisa menjawab pertanyaan.
     */
    public function getContextData(): array
    {
        $user    = Auth::user();
        $isAdmin = $user->hasRole('admin');

        return [
            'summary'          => $this->getSummary($isAdmin, $user->id),
            'top_exams'        => $this->getTopExams($isAdmin, $user->id),
            'exam_stats'       => $this->getExamStats($isAdmin, $user->id),
            'course_stats'     => $this->getCourseStats($isAdmin, $user->id),
            'top_students'     => $this->getTopStudents($isAdmin, $user->id),
            'failing_students' => $this->getFailingStudents($isAdmin, $user->id),
            'recent_sessions'  => $this->getRecentSessions($isAdmin, $user->id),
        ];
    }

    private function getSummary(bool $isAdmin, int $teacherId): array
    {
        $courseQuery = Course::where('status', 'published');
        if (! $isAdmin) $courseQuery->where('teacher_id', $teacherId);

        $courseIds = $courseQuery->pluck('id');

        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')->count('user_id');

        $totalSessions = ExamSession::whereHas('exam', fn ($q) =>
            $q->whereIn('course_id', $courseIds)
        )->where('status', 'graded');

        $passedSessions = (clone $totalSessions)->where('is_passed', true)->count();
        $totalCount     = (clone $totalSessions)->count();

        return [
            'total_courses'   => $courseQuery->count(),
            'total_students'  => $totalStudents,
            'total_exams'     => Exam::whereIn('course_id', $courseIds)->count(),
            'total_sessions'  => $totalCount,
            'passed_sessions' => $passedSessions,
            'pass_rate'       => $totalCount > 0 ? round($passedSessions / $totalCount * 100, 1) : 0,
            'avg_score'       => round((clone $totalSessions)->avg('score') ?? 0, 1),
        ];
    }

    private function getTopExams(bool $isAdmin, int $teacherId): array
    {
        return Exam::withCount([
                'sessions as total_attempts' => fn ($q) =>
                    $q->where('status', 'graded'),
                'sessions as passed_count' => fn ($q) =>
                    $q->where('status', 'graded')->where('is_passed', true),
            ])
            ->withAvg(['sessions as avg_score' => fn ($q) =>
                $q->where('status', 'graded')
            ], 'score')
            ->whereHas('course', function ($q) use ($isAdmin, $teacherId) {
                $q->where('status', 'published');
                if (! $isAdmin) $q->where('teacher_id', $teacherId);
            })
            ->orderByDesc('total_attempts')
            ->limit(10)
            ->get()
            ->map(fn ($exam) => [
                'ujian'            => $exam->title,
                'kursus'           => $exam->course->title ?? '—',
                'total_peserta'    => $exam->total_attempts,
                'jumlah_lulus'     => $exam->passed_count,
                'rata_rata_nilai'  => round($exam->avg_score ?? 0, 1),
                'nilai_lulus_min'  => $exam->pass_score,
                'tingkat_kelulusan' => $exam->total_attempts > 0
                    ? round($exam->passed_count / $exam->total_attempts * 100, 1) . '%'
                    : '0%',
            ])
            ->toArray();
    }

    private function getExamStats(bool $isAdmin, int $teacherId): array
    {
        return Exam::with('course:id,title')
            ->whereHas('course', function ($q) use ($isAdmin, $teacherId) {
                $q->where('status', 'published');
                if (! $isAdmin) $q->where('teacher_id', $teacherId);
            })
            ->get()
            ->map(function ($exam) {
                $sessions = ExamSession::where('exam_id', $exam->id)
                    ->where('status', 'graded');

                $highest = (clone $sessions)->max('score');
                $lowest  = (clone $sessions)->min('score');
                $avg     = (clone $sessions)->avg('score');
                $total   = (clone $sessions)->count();
                $passed  = (clone $sessions)->where('is_passed', true)->count();

                return [
                    'ujian'           => $exam->title,
                    'kursus'          => $exam->course->title ?? '—',
                    'nilai_tertinggi' => $highest ?? 'belum ada',
                    'nilai_terendah'  => $lowest ?? 'belum ada',
                    'rata_rata'       => round($avg ?? 0, 1),
                    'total_peserta'   => $total,
                    'lulus'           => $passed,
                    'tidak_lulus'     => $total - $passed,
                ];
            })
            ->toArray();
    }

    private function getCourseStats(bool $isAdmin, int $teacherId): array
    {
        $query = Course::with('teacher:id,name')
            ->withCount('enrollments')
            ->where('status', 'published');

        if (! $isAdmin) $query->where('teacher_id', $teacherId);

        return $query->orderByDesc('enrollments_count')
            ->get()
            ->map(fn ($c) => [
                'kursus'         => $c->title,
                'guru'           => $c->teacher->name ?? '—',
                'total_siswa'    => $c->enrollments_count,
                'siswa_selesai'  => CourseEnrollment::where('course_id', $c->id)
                    ->where('status', 'completed')->count(),
                'rata_progress'  => round(
                    CourseEnrollment::where('course_id', $c->id)->avg('progress_percent') ?? 0,
                    1
                ) . '%',
            ])
            ->toArray();
    }

    private function getTopStudents(bool $isAdmin, int $teacherId): array
    {
        return ExamSession::select('user_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total_exams'))
            ->where('status', 'graded')
            ->whereHas('exam.course', function ($q) use ($isAdmin, $teacherId) {
                if (! $isAdmin) $q->where('teacher_id', $teacherId);
            })
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->with('user:id,name,email')
            ->get()
            ->map(fn ($s) => [
                'nama'          => $s->user->name ?? '—',
                'email'         => $s->user->email ?? '—',
                'rata_nilai'    => round($s->avg_score, 1),
                'total_ujian'   => $s->total_exams,
            ])
            ->toArray();
    }

    private function getFailingStudents(bool $isAdmin, int $teacherId): array
    {
        return ExamSession::select('user_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total_exams'))
            ->where('status', 'graded')
            ->where('is_passed', false)
            ->whereHas('exam.course', function ($q) use ($isAdmin, $teacherId) {
                if (! $isAdmin) $q->where('teacher_id', $teacherId);
            })
            ->groupBy('user_id')
            ->orderBy('avg_score')
            ->limit(5)
            ->with('user:id,name,email')
            ->get()
            ->map(fn ($s) => [
                'nama'        => $s->user->name ?? '—',
                'email'       => $s->user->email ?? '—',
                'rata_nilai'  => round($s->avg_score, 1),
                'total_ujian' => $s->total_exams,
            ])
            ->toArray();
    }

    private function getRecentSessions(bool $isAdmin, int $teacherId): array
    {
        return ExamSession::with(['user:id,name', 'exam:id,title,pass_score'])
            ->where('status', 'graded')
            ->whereHas('exam.course', function ($q) use ($isAdmin, $teacherId) {
                if (! $isAdmin) $q->where('teacher_id', $teacherId);
            })
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'siswa'       => $s->user->name ?? '—',
                'ujian'       => $s->exam->title ?? '—',
                'nilai'       => $s->score,
                'status'      => $s->is_passed ? 'LULUS' : 'TIDAK LULUS',
                'waktu'       => $s->submitted_at?->format('d M Y H:i'),
            ])
            ->toArray();
    }
}
