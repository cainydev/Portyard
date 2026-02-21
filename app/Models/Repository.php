<?php

namespace App\Models;

use App\Enums\Roles;
use App\Policies\RepositoryPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(RepositoryPolicy::class)]
class Repository extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $with = ['space'];

    public static function fromPath(string $path): self
    {
        $parts = explode('/', $path, 2);

        if (count($parts) !== 2) {
            throw new ModelNotFoundException;
        }

        [$namespace, $name] = $parts;

        $space = Space::where('namespace', $namespace)->firstOrFail();

        return $space->repositories()
            ->where('name', $name)
            ->firstOrFail();
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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'name';
    }
}
