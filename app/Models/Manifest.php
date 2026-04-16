<?php

namespace App\Models;

use Cainy\Dockhand\Enums\MediaType;
use Cainy\Dockhand\Facades\Dockhand;
use Cainy\Dockhand\Resources\ImageManifest;
use Cainy\Dockhand\Resources\ManifestList;
use Cainy\Dockhand\Resources\ManifestResource;
use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Throwable;

class Manifest extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (Manifest $manifest) {
            $tag = $manifest->tags()->with('repository.space')->first();
            $space = $tag?->repository?->space;

            if ($manifest->isImageManifest() && $space) {
                $bytes = (int) $manifest->imageLayers()->sum('size_bytes');

                if ($bytes > 0) {
                    self::decrementSpaceStorage($space, $bytes);
                }
            } elseif ($manifest->isManifestList()) {
                $childManifestIds = $manifest->childManifestEntries()->pluck('child_manifest_id');

                // Let each child's own deleting hook handle its storage decrement.
                // We iterate rather than whereIn->delete() so hooks fire.
                Manifest::whereIn('id', $childManifestIds)->get()->each->delete();
            }
        });
    }

    private static function decrementSpaceStorage(\App\Models\Space $space, int $bytes): void
    {
        $space->storage_used_bytes = max(0, ((int) $space->storage_used_bytes) - $bytes);
        $space->save();
    }

    protected $fillable = [
        'digest',
        'media_type',
        'size_bytes',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'media_type' => MediaType::class,
        ];
    }

    /**
     * Create a new manifest from a resource.
     *
     * @throws Throwable
     */
    public static function createFromResource(ManifestResource $resource): Manifest
    {
        return DB::transaction(function () use ($resource) {
            /* @var Manifest $manifest */
            $manifest = Manifest::create([
                'digest' => $resource->digest,
                'media_type' => $resource->mediaType,
                'size_bytes' => $resource->getSize(),
                'content' => $resource->toArray(),
            ]);

            if ($resource instanceof ManifestList) {
                foreach ($resource->manifests as $manifestListEntry) {
                    $childManifest = Dockhand::getManifest($manifestListEntry->repository, $manifestListEntry->digest);

                    if ($childManifest->isManifestList()) {
                        throw new Exception('Manifest list inside manifest list is not supported');
                    }

                    $childManifestModel = Manifest::createFromResource($childManifest);

                    $manifest->childManifestEntries()->create([
                        'child_manifest_id' => $childManifestModel->id,
                        'platform_os' => $manifestListEntry->platform->os,
                        'platform_architecture' => $manifestListEntry->platform->architecture,
                        'platform_variant' => $manifestListEntry->platform->variant,
                    ]);
                }
            } elseif ($resource instanceof ImageManifest) {
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
                        'media_type' => $layer->mediaType->toString(),
                    ]);
                }
            }

            return $manifest;
        });
    }

    /**
     * Returns true if this manifest is an Image Manifest List (multiple).
     */
    public function isManifestList(): bool
    {
        return $this->media_type->isManifestList();
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
     */
    public function isImageManifest(): bool
    {
        return $this->media_type->isImageManifest();
    }
}
