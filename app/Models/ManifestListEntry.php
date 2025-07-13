<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManifestListEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'list_manifest_id',
        'child_manifest_id',
        'platform_os',
        'platform_architecture',
        'platform_variant',
        'platform_os_version',
        'platform_os_features',
        'platform_features',
    ];

    protected $casts = [
        'platform_os_features' => 'array',
        'platform_features' => 'array',
    ];

    public function listManifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class, 'list_manifest_id');
    }

    public function childManifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class, 'child_manifest_id');
    }
}
