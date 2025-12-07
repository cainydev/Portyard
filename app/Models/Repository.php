<?php

namespace App\Models;

use App\Enums\Roles;
use App\Policies\RepositoryPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function auth;

#[UsePolicy(RepositoryPolicy::class)]
class Repository extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $with = ['space'];

    public static function booted(): void
    {
        self::created(function (self $repository) {
            if (auth()->check()) {
                $repository->owners()->attach(auth()->user());
            }
        });
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Member::class)
            ->withPivotValue('role', Roles::Owner)
            ->withTimestamps();
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Member::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function maintainers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Member::class)
            ->withPivotValue('role', Roles::Maintainer)
            ->withTimestamps();
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Member::class)
            ->withPivotValue('role', Roles::Developer)
            ->withTimestamps();
    }

    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(Member::class)
            ->withPivotValue('role', Roles::Viewer)
            ->withTimestamps();
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function path(): Attribute
    {
        return Attribute::make(get: fn () => "{$this->space->namespace}/{$this->name}");
    }
}
