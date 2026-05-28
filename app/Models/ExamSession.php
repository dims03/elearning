<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'attempt_number',
        'status',
        'score',
        'is_passed',
        'started_at',
        'submitted_at',
        'expires_at',
    ];

    protected $casts = [
        'is_passed'    => 'boolean',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getRemainingSeconds(): int
    {
        if (! $this->expires_at) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    public function calculateScore(): int
    {
        $totalPoints  = $this->exam->questions->sum('points');
        $earnedPoints = $this->answers->sum('score_given');

        if ($totalPoints === 0) {
            return 0;
        }

        return (int) round(($earnedPoints / $totalPoints) * 100);
    }

    public function submit(): void
    {
        // Auto grade semua jawaban multiple choice dulu
        $this->answers->each(fn ($answer) => $answer->autoGrade());

        $score = $this->calculateScore();

        $this->update([
            'status'       => 'graded',
            'score'        => $score,
            'is_passed'    => $score >= $this->exam->pass_score,
            'submitted_at' => now(),
        ]);
    }
}
