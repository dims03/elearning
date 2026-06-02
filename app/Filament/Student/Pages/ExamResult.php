<?php

namespace App\Filament\Student\Pages;

use App\Models\ExamSession;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamResult extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.student.pages.exam-result';

    public int $session = 0;
    public ?ExamSession $currentSession = null;
    public ?bool $isPassed = null;
    public ?int $score = null;
    public ?int $passingScore = null;
    public $answers = null;
    public $exam = null;
    public $sessionData = null;

    public function mount(): void
    {
        $this->session = (int) request()->query('session');

        $this->currentSession = ExamSession::with([
            'exam.questions.options',
            'answers.question.options',
            'answers.selectedOption',
            'exam.course',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($this->session);

        $this->exam        = $this->currentSession->exam;
        $this->sessionData = $this->currentSession;
        $this->isPassed    = $this->currentSession->is_passed;
        $this->score       = $this->currentSession->score;
        $this->passingScore = $this->currentSession->exam->pass_score;
        $this->answers     = $this->currentSession->answers->keyBy('question_id');
    }

    public function getViewData(): array
    {
        $session  = $this->currentSession;
        $exam     = $session->exam;
        $answers  = $session->answers->keyBy('question_id');

        return [
            'session'      => $session,
            'exam'         => $exam,
            'answers'      => $answers,
            'isPassed'     => $session->is_passed,
            'score'        => $session->score,
            'passingScore' => $exam->pass_score,
        ];
    }
}
