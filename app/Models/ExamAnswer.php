<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_session_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'score_given',
        'teacher_feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Auto-grade untuk soal multiple_choice dan true_false.
     * Untuk essay, guru harus grade manual.
     */
    public function autoGrade(): void
    {
        if (! $this->question->isMultipleChoice()) {
            return;
        }

        $correct = $this->selected_option_id !== null
            && $this->selectedOption?->is_correct === true;

        $this->update([
            'is_correct'  => $correct,
            'score_given' => $correct ? $this->question->points : 0,
        ]);
    }

    /**
     * Grade manual oleh guru (untuk soal essay).
     */
    public function gradeEssay(int $scoreGiven, ?string $feedback = null): void
    {
        $this->update([
            'score_given'      => min($scoreGiven, $this->question->points),
            'is_correct'       => $scoreGiven > 0,
            'teacher_feedback' => $feedback,
        ]);
    }
}