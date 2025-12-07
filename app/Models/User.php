<?php

namespace App\Models;

use App\Enums\Roles;
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

class User extends Authenticatable
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

        $fallback = $this->spaces()->where('namespace', $this->username)->first()
            ?? $this->spaces()->first();

        if ($fallback) {
            session(['current_space_id' => $fallback->id]);
        }

        return $fallback;
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
