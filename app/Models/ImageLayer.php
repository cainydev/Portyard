<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageLayer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'digest',
        'manifest_id',
        'sort_order',
        'size_bytes',
        'media_type',
        'is_empty_layer',
    ];

    protected function casts(): array
    {
        return [
            'is_empty_layer' => 'boolean',
        ];
    }

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class);
    }
}
