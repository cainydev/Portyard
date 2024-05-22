<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Repository::class);
    }

    public function namespace(): Attribute
    {
        return new Attribute(get: fn() => str($this->name)->slug());
    }

    public function intersectClaims(string $resourceName, Collection $actions): array|Collection
    {
        $segments = str($resourceName)->explode('/');

        Log::channel('stderr')
            ->info("Intersecting claims for " . $resourceName);
        Log::channel('stderr')
            ->info("Getting scopes: " . $actions->join(', '));

        if ($segments->count() < 2) {
            Log::channel('stderr')->error('Segment count < 2');
            return [];
        }

        if ($segments[0] != $this->namespace) {
            Log::channel('stderr')->error('First segment not namespace');
            return [];
        }

        if ($this->repositories()->where('name', $segments[1])->count() == 1) {
            Log::channel('stderr')->info('Alright! Scopes valid.');
            return $actions;
        } else {
            Log::channel('stderr')->info('Repo doesn\'t exists, creating...');
            $this->repositories()->create([
                'name' => $segments[1]
            ]);
            Log::channel('stderr')->info('Repository created!');
            return $actions;
        }
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
