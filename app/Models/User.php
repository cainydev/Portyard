<?php

namespace App\Models;

use App\Enums\Roles;
use App\Events\User\UserUpdated;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

use function str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'spaces',
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

        static::updated(function (User $user) {
            if ($user->wasChanged(['name', 'email', 'password'])) {
                UserUpdated::dispatch($user);
            }
        });
    }

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Repository::class);
    }

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)
            ->withPivot(['role']);
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

    public function currentSpace(): Space
    {
        if (session()->has('current_space_id')) {
            $spaceId = session('current_space_id');

            $space = $this->spaces()->find($spaceId);

            if ($space) {
                return $space;
            }
        }

        session(['current_space_id' => $this->personalSpace->id]);

        return $this->personalSpace;
    }

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'space_user')
            ->using(Member::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /** @return Attribute<Space> */
    public function personalSpace(): Attribute
    {
        return Attribute::make(get: fn () => $this->spaces()->whereNamespace($this->slug)->first() ?? $this->spaces()->first());
    }

    public function switchSpace(Space $space): void
    {
        if ($this->spaces()->where('spaces.id', $space->id)->exists()) {
            session(['current_space_id' => $space->id]);
        }
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'name';
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
