<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'question_text',
        'type',
        'points',
        'order',
        'explanation',
        'image',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getCorrectOption(): ?QuestionOption
    {
        return $this->options()->where('is_correct', true)->first();
    }

    public function isMultipleChoice(): bool
    {
        return in_array($this->type, ['multiple_choice', 'true_false']);
    }

    public function isEssay(): bool
    {
        return $this->type === 'essay';
    }
}