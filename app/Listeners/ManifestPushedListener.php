<?php

namespace App\Listeners;

use App\Models\Manifest;
use App\Models\Repository;
use App\Models\Space;
use App\Models\Tag;
use App\Models\User;
use App\Services\NamingService;
use Cainy\Dockhand\Events\ManifestPushedEvent;
use Cainy\Dockhand\Facades\Dockhand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function now;

class ManifestPushedListener
{
    /**
     * @throws Throwable
     */
    public function handle(ManifestPushedEvent $event): void
    {
        Log::info('ManifestPushedListener: handle() ', [
            'repository' => $event->targetRepository,
            'tag' => $event->targetTag,
            'digest' => $event->targetDigest,
            'actor' => $event->actorName,
        ]);

        $manifest = Dockhand::getManifest($event->targetRepository, $event->targetDigest);

        if ($manifest === null) {
            Log::error('ManifestPushedListener: Manifest not found in registry');

            return;
        }

        $user = User::where('email', $event->actorName)->first();

        if (! $user) {
            Log::warning('ManifestPushedListener: Unknown actor', ['actor' => $event->actorName]);

            return;
        }

        try {
            $repo = Repository::fromPath($event->targetRepository);
        } catch (ModelNotFoundException) {
            $repo = $this->autoCreateRepository($event->targetRepository);

            if (! $repo) {
                return;
            }
        }

        Log::info("ManifestPushedListener: Repository resolved (ID: {$repo->id})");

        DB::transaction(function () use ($event, $user, $repo, $manifest) {
            $manifestModel = Manifest::createFromResource($manifest);

            $tag = Tag::firstWhere([
                'repository_id' => $repo->id,
                'name' => $event->targetTag,
            ]);

            if ($tag) {
                $tag->update([
                    'user_id' => $user->id,
                    'manifest_id' => $manifestModel->id,
                    'last_pushed' => now(),
                ]);
                Log::info("ManifestPushedListener: Tag updated '{$tag->name}'");
            } else {
                Tag::create([
                    'repository_id' => $repo->id,
                    'name' => $event->targetTag,
                    'user_id' => $user->id,
                    'manifest_id' => $manifestModel->id,
                    'last_pushed' => now(),
                ]);
                Log::info("ManifestPushedListener: Tag created '{$event->targetTag}'");
            }

            $bytes = (int) $manifestModel->imageLayers()->sum('size_bytes');

            if ($manifestModel->isManifestList()) {
                $childManifestIds = $manifestModel->childManifestEntries()->pluck('child_manifest_id');
                $bytes = (int) \App\Models\ImageLayer::whereIn('manifest_id', $childManifestIds)->sum('size_bytes');
            }

            if ($bytes > 0) {
                $repo->space->increment('storage_used_bytes', $bytes);
                Log::info("ManifestPushedListener: Incremented storage by {$bytes} bytes for space '{$repo->space->namespace}'");
            }
        });
    }

    private function autoCreateRepository(string $path): ?Repository
    {
        $parts = explode('/', $path, 2);

        if (count($parts) !== 2) {
            Log::error("ManifestPushedListener: Invalid repository path '{$path}'");

            return null;
        }

        [$namespace, $repoName] = $parts;

        if (! NamingService::isValidRepositoryName($repoName)) {
            Log::error("ManifestPushedListener: Invalid repository name '{$repoName}'");

            return null;
        }

        $space = Space::where('namespace', $namespace)->first();

        if (! $space) {
            Log::error("ManifestPushedListener: Space '{$namespace}' not found");

            return null;
        }

        $repository = $space->repositories()->create([
            'name' => $repoName,
            'public' => false,
        ]);

        Log::info("ManifestPushedListener: Auto-created repository {$path}");

        return $repository;
    }
}
