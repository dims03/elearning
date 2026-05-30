<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Lesson extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'content',
        'type',
        'video_url',
        'attachment',
        'duration_minutes',
        'order',
        'is_published',
        'is_free_preview',
    ];

    protected $casts = [
        'is_published'    => 'boolean',
        'is_free_preview' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /** Akses course langsung dari lesson tanpa query chapter dulu */
    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Chapter::class,
            'id',         // chapters.id
            'id',         // courses.id
            'chapter_id', // lessons.chapter_id
            'course_id'   // chapters.course_id
        );
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class)->orderBy('created_at');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isCompletedBy(User $user): bool
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->exists();
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (blank($this->attachment)) {
            return null;
        }

        if (Storage::disk('public')->exists($this->attachment)) {
            return Storage::disk('public')->url($this->attachment);
        }

        if (Storage::disk('local')->exists($this->attachment)) {
            Storage::disk('public')->makeDirectory(dirname($this->attachment));
            Storage::disk('public')->put($this->attachment, Storage::disk('local')->get($this->attachment));

            return Storage::disk('public')->url($this->attachment);
        }

        return null;
    }
}
