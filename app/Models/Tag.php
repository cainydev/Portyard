<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'last_pushed' => 'datetime',
    ];

    protected static function boot(): void
    {
        static::creating(function (Tag $model) {
            $model->last_pushed = now();
        });
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }
}
