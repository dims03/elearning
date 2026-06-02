<?php

namespace App\Filament\Student\Pages;

use App\Models\CourseEnrollment;
use App\Models\Exam;
use App\Models\ExamSession;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class MyExam extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;
    protected static ?string $title = 'My Exam';
    protected static ?string $navigationLabel = 'My Exam';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.student.pages.my-exam';

    public function getViewData(): array
    {
        $userId = Auth::id();

        $enrolledCourseIds = CourseEnrollment::where('user_id', $userId)
            ->pluck('course_id');

        $exams = Exam::with(['course'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'published')
            ->get()
            ->map(function ($exam) use ($userId) {
                $sessions = ExamSession::where('user_id', $userId)
                    ->where('exam_id', $exam->id)
                    ->orderBy('attempt_number')
                    ->get();

                $lastSession   = $sessions->last();
                $attemptCount  = $sessions->count();
                $canAttempt    = $exam->canAttemptBy(auth()->user());
                $bestScore     = $sessions->where('status', 'graded')->max('score');
                $hasPassed     = $sessions->where('is_passed', true)->isNotEmpty();
                $inProgress    = $sessions->where('status', 'in_progress')->first();

                return [
                    'exam'          => $exam,
                    'sessions'      => $sessions,
                    'lastSession'   => $lastSession,
                    'attemptCount'  => $attemptCount,
                    'canAttempt'    => $canAttempt,
                    'bestScore'     => $bestScore,
                    'hasPassed'     => $hasPassed,
                    'inProgress'    => $inProgress,
                ];
            });

        return ['exams' => $exams];
    }
}
