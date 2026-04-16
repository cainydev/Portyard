<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasUuids;

    public const int DEFAULT_EXPIRY_DAYS = 14;

    protected $fillable = [
        'space_id',
        'email',
        'role',
        'token',
        'invited_by',
        'expires_at',
    ];

    /**
     * @return array{role: string, accepted_at: string, declined_at: string, expires_at: string}
     */
    protected function casts(): array
    {
        return [
            'role' => Roles::class,
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            if (! $invitation->token) {
                $invitation->token = Str::random(64);
            }

            if (! $invitation->expires_at) {
                $invitation->expires_at = now()->addDays(self::DEFAULT_EXPIRY_DAYS);
            }
        });
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
