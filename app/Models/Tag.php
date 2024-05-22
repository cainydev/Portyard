<?php

namespace App\Models;

use App\Traits\IsCreatedByUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Tag extends Model
{
    use HasFactory, HasUuids, IsCreatedByUser;

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Repository::class);
    }
}
