<?php

namespace App\Models;

use App\Enums\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'request_id',
        'space_id',
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'action' => Action::class,
            'metadata' => 'array',
        ];
    }

    /**
     * The Space where this activity happened.
     * Nullable because some events (like Login) are global.
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * The User who performed the action.
     * Nullable because system actions (like auto-cleanup) might have no user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The object that was modified (Repository, Tag, Member, Space).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to find all activities in a specific request.
     */
    public function scopeForRequest(Builder $query, string $requestId): void
    {
        $query->where('request_id', $requestId);
    }
}
