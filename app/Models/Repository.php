<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use function auth;

class Repository extends Model
{
    use HasUuids;

    protected $guarded = [];

    public static function booted(): void
    {
        self::created(function (self $repository) {
            $repository->owners()->attach(auth()->user());
        });
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivotValue('role', Roles::Owner);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role']);
    }

    public function maintainers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivotValue('role', Roles::Maintainer);
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivotValue('role', Roles::Developer);
    }

    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivotValue('role', Roles::Viewer);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
