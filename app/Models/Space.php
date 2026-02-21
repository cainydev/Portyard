<?php

namespace App\Models;

use App\Enums\Roles;
use App\Events\Space\SpaceCreated;
use App\Events\Space\SpaceDeleted;
use App\Events\Space\SpaceUpdated;
use App\Policies\SpacePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function abort;

#[UsePolicy(SpacePolicy::class)]
class Space extends Model
{
    use HasUuids;

    protected $guarded = [];

    public static function current(): self
    {
        if (! auth()->check()) {
            abort(403);
        }

        return auth()->user()->currentSpace();
    }

    protected static function booted(): void
    {
        static::created(function (Space $space) {
            SpaceCreated::dispatch($space, auth()->user() ?? $space->owners()->first());
        });

        static::updated(function (Space $space) {
            if ($space->wasChanged(['name', 'namespace'])) {
                SpaceUpdated::dispatch($space, auth()->user());
            }
        });

        static::deleted(function (Space $space) {
            SpaceDeleted::dispatch($space, auth()->user());
        });
    }

    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', Roles::Owner);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'space_user')
            ->using(Member::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function maintainers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', Roles::Maintainer);
    }

    public function developers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', Roles::Developer);
    }

    public function viewers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', Roles::Viewer);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'namespace';
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
    }
}
