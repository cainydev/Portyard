<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageConfig extends Model
{
    use HasUuids;

    protected $fillable = [
        'manifest_id',
        'digest',
        'architecture',
        'os',
        'variant',
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }
}
