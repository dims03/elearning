<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'pass_score',
        'max_attempts',
        'is_randomized',
        'show_result_immediately',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_randomized'           => 'boolean',
        'show_result_immediately' => 'boolean',
        'start_at'                => 'datetime',
        'end_at'                  => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeAvailable($query)
    {
        return $query->published()
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }
        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }
        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getAttemptsCountFor(User $user): int
    {
        return $this->sessions()->where('user_id', $user->id)->count();
    }

    public function canAttemptBy(User $user): bool
    {
        return $this->isAvailable()
            && $this->getAttemptsCountFor($user) < $this->max_attempts;
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions->sum('points');
    }
}