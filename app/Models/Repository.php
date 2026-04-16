<?php

namespace App\Models;

use App\Events\Repository\RepositoryCreated;
use App\Events\Repository\RepositoryDeleted;
use App\Events\Repository\RepositoryUpdated;
use App\Policies\RepositoryPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(RepositoryPolicy::class)]
class Repository extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'space_id',
        'name',
        'description',
        'overview',
        'public',
    ];

    protected $with = ['space'];

    protected static function booted(): void
    {
        static::created(function (Repository $repository) {
            RepositoryCreated::dispatch($repository, auth()->user());
        });

        static::updated(function (Repository $repository) {
            if ($repository->wasChanged(['name', 'public'])) {
                RepositoryUpdated::dispatch($repository, auth()->user());
            }
        });

        static::deleting(function (Repository $repository) {
            $repository->tags->each->delete();
        });

        static::deleted(function (Repository $repository) {
            RepositoryDeleted::dispatch($repository, auth()->user());
        });
    }

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

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
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
