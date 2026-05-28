<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons->sum('duration_minutes');
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons->count();
    }
}
