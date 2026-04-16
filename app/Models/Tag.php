<?php

namespace App\Models;

use App\Enums\WebhookTrigger;
use App\Services\WebhookDispatcher;
use Cainy\Dockhand\Facades\Dockhand;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Tag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'repository_id',
        'manifest_id',
        'name',
        'user_id',
        'last_pushed',
    ];

    protected function casts(): array
    {
        return [
            'last_pushed' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tag $model) {
            $model->last_pushed = now();
        });

        static::deleting(function (Tag $tag) {
            $tag->loadMissing(['repository.space', 'manifest.imageConfig', 'manifest.childManifestEntries']);

            if ($tag->repository) {
                app(WebhookDispatcher::class)->dispatch($tag->repository, WebhookTrigger::TagDeleted, [
                    'tag' => $tag,
                    'tag_name' => $tag->name,
                    'manifest' => $tag->manifest,
                ]);
            }

            if (! $tag->manifest) {
                return;
            }

            $otherTagsExist = Tag::where('manifest_id', $tag->manifest_id)
                ->where('id', '!=', $tag->id)
                ->exists();

            if ($otherTagsExist) {
                return;
            }

            try {
                Dockhand::deleteManifest($tag->repository->path, $tag->manifest->digest);
            } catch (\Exception $e) {
                Log::error("Failed to delete manifest from registry: {$e->getMessage()}");
            }

            DB::transaction(fn () => $tag->manifest->delete());
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
