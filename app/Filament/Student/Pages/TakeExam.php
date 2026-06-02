<?php

namespace App\Filament\Student\Pages;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class TakeExam extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.student.pages.take-exam';

    public int $exam = 0;

    public ?ExamSession $session = null;
    public ?Exam $currentExam = null;
    public array $answers = [];
    public int $remainingSeconds = 0;

    public function mount(): void
    {
        $this->exam = (int) request()->query('exam');

        if (! $this->exam) {
            redirect()->to(MyExams::getUrl());
            return;
        }

        $this->currentExam = Exam::with([
            'questions.options',
            'course',
        ])->findOrFail($this->exam);

        $existing = ExamSession::where('user_id', Auth::id())
            ->where('exam_id', $this->exam)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            $this->session = $existing;
        } else {
            $attempts = ExamSession::where('user_id', Auth::id())
                ->where('exam_id', $this->exam)
                ->count();

            if ($attempts >= $this->currentExam->max_attempts) {
                Notification::make()
                    ->title('Kamu sudah mencapai batas percobaan ujian ini.')
                    ->warning()
                    ->send();

                redirect()->to(MyExams::getUrl());
                return;
            }

            $this->session = ExamSession::create([
                'exam_id'        => $this->exam,
                'user_id'        => Auth::id(),
                'attempt_number' => $attempts + 1,
                'status'         => 'in_progress',
                'started_at'     => now(),
                'expires_at'     => now()->addMinutes($this->currentExam->duration_minutes),
            ]);
        }

        $this->remainingSeconds = max(0, now()->diffInSeconds($this->session->expires_at, false));

        $questions = $this->currentExam->questions;
        if ($this->currentExam->is_randomized) {
            $questions = $questions->shuffle();
        }

        foreach ($questions as $q) {
            $existing_answer = ExamAnswer::where('exam_session_id', $this->session->id)
                ->where('question_id', $q->id)
                ->first();

            $this->answers[$q->id] = $existing_answer
                ? ($existing_answer->selected_option_id ?? $existing_answer->answer_text ?? '')
                : '';
        }
    }

     public function saveAnswer(int $questionId, mixed $value): void
    {
        $this->answers[$questionId] = $value;

        ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $this->session->id,
                'question_id'     => $questionId,
            ],
            $this->buildAnswerData($questionId, $value)
        );
    }

    private function buildAnswerData(int $questionId, mixed $value): array
    {
        $question = $this->currentExam->questions->find($questionId);

        if ($question->isEssay()) {
            return ['answer_text' => $value, 'selected_option_id' => null];
        }

        return ['selected_option_id' => $value ?: null, 'answer_text' => null];
    }

    public function submitExam(): void
    {
        foreach ($this->answers as $questionId => $value) {
            ExamAnswer::updateOrCreate(
                [
                    'exam_session_id' => $this->session->id,
                    'question_id'     => $questionId,
                ],
                $this->buildAnswerData($questionId, $value)
            );
        }

        $this->session->answers->each(fn ($a) => $a->autoGrade());

        // Hitung score
        $score    = $this->session->calculateScore();
        $isPassed = $score >= $this->currentExam->pass_score;

        $this->session->update([
            'status'       => 'graded',
            'score'        => $score,
            'is_passed'    => $isPassed,
            'submitted_at' => now(),
        ]);

        // Refresh session
        $this->session->refresh();

        Notification::make()
            ->title($isPassed
                ? "Selamat! Kamu lulus dengan nilai {$score}% 🎉"
                : "Ujian selesai. Nilai kamu {$score}%. Nilai lulus: {$this->currentExam->pass_score}%")
            ->status($isPassed ? 'success' : 'warning')
            ->send();

        redirect()->to(ExamResult::getUrl(['session' => $this->session->id]));
    }

    public function timeUp(): void
    {
        Notification::make()
            ->title('Waktu habis! Ujian otomatis dikumpulkan.')
            ->warning()
            ->send();

        $this->submitExam();
    }

    public function getViewData(): array
    {
        $questions = $this->currentExam->questions;

        if ($this->currentExam->is_randomized) {
            // Gunakan seed dari session id agar urutan konsisten per session
            $questions = $questions->sortBy(fn ($q) =>
                crc32($this->session->id . $q->id)
            )->values();
        }

        return [
            'exam'             => $this->currentExam,
            'session'          => $this->session,
            'questions'        => $questions,
            'remainingSeconds' => $this->remainingSeconds,
            'totalQuestions'   => $questions->count(),
            'answeredCount'    => collect($this->answers)->filter(fn ($v) => $v !== '' && $v !== null)->count(),
        ];
    }
}