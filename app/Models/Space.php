<?php

namespace App\Models;

use App\Enums\Roles;
use App\Events\Space\SpaceCreated;
use App\Events\Space\SpaceDeleted;
use App\Events\Space\SpaceUpdated;
use App\Policies\SpacePolicy;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[UsePolicy(SpacePolicy::class)]
class Space extends Model
{
    use HasUuids;

    /** 5 GB storage limit per space during beta. */
    public const int BETA_STORAGE_LIMIT_BYTES = 5 * 1024 * 1024 * 1024;

    /** Maximum number of spaces a user can own during beta. */
    public const int BETA_MAX_SPACES_PER_USER = 3;

    protected $fillable = [
        'name',
        'namespace',
        'description',
        'storage_used_bytes',
    ];

    public static function current(): self
    {
        if (! auth()->check()) {
            throw new AuthenticationException;
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

    public function isOverQuota(): bool
    {
        return $this->storage_used_bytes >= self::BETA_STORAGE_LIMIT_BYTES;
    }

    public function storageUsedPercent(): float
    {
        return min(100, ($this->storage_used_bytes / self::BETA_STORAGE_LIMIT_BYTES) * 100);
    }

    public function recalculateStorage(): void
    {
        $computed = (int) DB::table('repositories')
            ->join('tags', 'tags.repository_id', '=', 'repositories.id')
            ->join('manifests', 'manifests.id', '=', 'tags.manifest_id')
            ->join('image_layers', 'image_layers.manifest_id', '=', 'manifests.id')
            ->where('repositories.space_id', $this->id)
            ->sum('image_layers.size_bytes');

        $this->update(['storage_used_bytes' => $computed]);
    }
}
