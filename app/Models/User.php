<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

use function str;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

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

    protected static function booted(): void
    {
        static::saving(function (User $model) {
            $model->slug = str($model->name)->lower()->slug();
        });

        static::created(function (User $model) {
            $space = Space::create([
                'name' => $model->name."'s Space",
                'namespace' => str($model->name)->slug()->lower(),
            ]);

            $space->users()->attach($model, ['role' => Roles::Owner->value]);
        });
    }

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Repository::class);
    }

    public function namespace(): Attribute
    {
        return new Attribute(get: fn () => str($this->name)->slug()->lower());
    }

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)
            ->withPivot(['role']);
    }

    public function ownedRepositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)
            ->withPivotValue('role', 'owner');
    }

    public function collaborations(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function currentSpace(): ?Space
    {
        $spaceId = session('current_space_id');

        if (! $spaceId) {
            return $this->spaces()->first();
        }

        return $this->spaces()->find($spaceId);
    }

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'space_user')
            ->using(Member::class)
            ->withPivot(['role'])
            ->withTimestamps();
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
