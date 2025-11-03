<?php

namespace App\Models;

use Exception;
use Cainy\Dockhand\Enums\MediaType;
use Cainy\Dockhand\Facades\Dockhand;
use Cainy\Dockhand\Resources\ImageManifest;
use Cainy\Dockhand\Resources\ManifestList;
use Cainy\Dockhand\Resources\ManifestResource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Throwable;

class Manifest extends Model
{
    use HasUuids;

    protected $fillable = [
        'digest',
        'media_type',
        'size_bytes',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'media_type' => MediaType::class,
    ];

    /**
     * Create a new manifest from a resource.
     *
     * @param ManifestResource $resource
     * @return Manifest
     * @throws Throwable
     */
    public static function createFromResource(ManifestResource $resource): Manifest
    {
        DB::beginTransaction();

        /* @var Manifest $manifest */
        $manifest = Manifest::create([
            'digest' => $resource->digest,
            'media_type' => $resource->mediaType,
            'size_bytes' => $resource->getSize(),
            'content' => $resource->toArray(),
        ]);

        if ($resource instanceof ManifestList) {
            foreach ($resource->manifests as $manifestListEntry) {
                $childManifest = Dockhand::getManifestFromManifestListEntry($manifestListEntry);

                if ($childManifest->isManifestList()) {
                    throw new Exception("Manifest list inside manifest list is not supported");
                }

                $childManifestModel = Manifest::createFromResource($childManifest);

                $manifest->childManifestEntries()->create([
                    'child_manifest_id' => $childManifestModel->id,
                    'platform_os' => $manifestListEntry->platform->os,
                    'platform_architecture' => $manifestListEntry->platform->architecture,
                    'platform_variant' => $manifestListEntry->platform->variant,
                ]);
            }
        } else if ($resource instanceof ImageManifest) {
            $config = Dockhand::getImageConfigFromDescriptor($resource->config);

            $manifest->imageConfig()->create([
                'digest' => $config->digest,
                'architecture' => $config->platform->architecture,
                'os' => $config->platform->os,
                'variant' => $config->platform->variant,
            ]);

            $order = 0;
            foreach ($resource->layers as $layer) {
                $manifest->imageLayers()->create([
                    'digest' => $layer->digest,
                    'sort_order' => $order++,
                    'size_bytes' => $layer->size,
                    'media_type' => $layer->mediaType->toString()
                ]);
            }
        }

        DB::commit();

        return $manifest;
    }

    /**
     * Returns true if this manifest is an Image Manifest List (multiple).
     *
     * @return bool
     */
    public function isManifestList(): bool
    {
        return $this->media_type->isImageManifestList();
    }

    /**
     * If this manifest is a Manifest List/Index, these are its child manifest entries.
     */
    public function childManifestEntries(): HasMany
    {
        return $this->hasMany(ManifestListEntry::class, 'list_manifest_id');
    }

    /**
     * The structured image configuration if this is an Image Manifest.
     */
    public function imageConfig(): HasOne
    {
        return $this->hasOne(ImageConfig::class);
    }

    /**
     * The layers if this is an Image Manifest.
     */
    public function imageLayers(): HasMany
    {
        return $this->hasMany(ImageLayer::class);
    }

    /**
     * Tags that point to this manifest.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * If this manifest is an Image Manifest, these are the Manifest List Entries it's part of.
     */
    public function parentManifestListEntries(): HasMany
    {
        return $this->hasMany(ManifestListEntry::class, 'child_manifest_id');
    }

    /**
     * Returns true if this manifest is an Image Manifest (single).
     *
     * @return bool
     */
    public function isImageManifest(): bool
    {
        return $this->media_type->isImageManifest();
    }
}
