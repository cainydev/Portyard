<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use function str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

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

    protected static function boot(): void
    {
        static::saving(function (User $model) {
            $model->slug = str($model->name)->lower()->slug();
        });

        parent::boot();
    }

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Repository::class);
    }

    public function namespace(): Attribute
    {
        return new Attribute(get: fn() => str($this->name)->slug());
    }

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)
            ->withPivot(['role']);
    }

    public function collaborations(): HasMany
    {
        return $this->hasMany(Collaborator::class);
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
