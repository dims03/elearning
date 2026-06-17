<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamSession;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamResult extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.student.pages.exam-result';

    public int $sessionId = 0;
    public ?ExamSession $currentSession = null;
    public ?bool $isPassed = null;
    public ?int $score = null;
    public ?int $passingScore = null;
    public $answers = null;
    public $exam = null;
    public $sessionData = null;
    public $attemptHistory = null;

    public function mount(): void
    {
        $this->sessionId = (int) request()->query('session');

        $this->currentSession = ExamSession::with([
            'exam.questions.options',
            'answers.question.options',
            'answers.selectedOption',
            'exam.course',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($this->sessionId);

        $this->exam        = $this->currentSession->exam;
        $this->sessionData = $this->currentSession;
        $this->isPassed    = $this->currentSession->is_passed;
        $this->score       = $this->currentSession->score;
        $this->passingScore = $this->currentSession->exam->pass_score;
        $this->answers     = $this->currentSession->answers->keyBy('question_id');
        $this->attemptHistory = ExamSession::query()
            ->where('user_id', Auth::id())
            ->where('exam_id', $this->currentSession->exam_id)
            ->where('status', 'graded')
            ->orderByDesc('attempt_number')
            ->get();
    }

    public function getViewData(): array
    {
        $session  = $this->currentSession;
        $exam     = $session->exam;
        $answers  = $session->answers->keyBy('question_id');

        return [
            'currentSession' => $session,
            'exam'           => $exam,
            'answers'        => $answers,
            'isPassed'       => $session->is_passed,
            'score'          => $session->score,
            'passingScore'   => $exam->pass_score,
            'attemptHistory' => $this->attemptHistory,
        ];
    }
}
