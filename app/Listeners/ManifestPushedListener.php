<?php

namespace App\Listeners;

use App\Models\Manifest;
use App\Models\Repository;
use App\Models\Tag;
use App\Models\User;
use Cainy\Dockhand\Events\ManifestPushedEvent;
use Cainy\Dockhand\Facades\Dockhand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ManifestPushedListener
{
    /**
     * @throws Throwable
     */
    public function handle(ManifestPushedEvent $event): void
    {
        Log::info("ManifestPushedListener: handle() ", [
            'repository' => $event->targetRepository,
            'tag' => $event->targetTag,
            'digest' => $event->targetDigest,
            'actor' => $event->actorName,
            'size' => $event->targetSize,
            'timestamp' => $event->timestamp,
            'action' => $event->action,
            'url' => $event->targetUrl,
        ]);

        $manifest = Dockhand::getManifest($event->targetRepository, $event->targetDigest);

        if ($manifest === null) {
            Log::error("ManifestPushedHandler: Manifest not found", [
                'repository' => $event->targetRepository,
                'digest' => $event->targetDigest,
            ]);
            return;
        }

        $user = User::where('email', $event->actorName)->firstOrFail();
        Log::info("ManifestPushedListener: User found", $user->toArray());

        $repo = Repository::where('path', $event->targetRepository)->firstOrFail();
        Log::info("ManifestPushedListener: Repo found", $repo->toArray());

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
                    'last_pushed' => now()
                ]);

                Log::info("ManifestPushedListener: Tag updated", $tag->toArray());
            } else {
                Tag::create([
                    'repository_id' => $repo->id,
                    'name' => $event->targetTag,
                    'user_id' => $user->id,
                    'manifest_id' => $manifestModel->id,
                    'last_pushed' => now()
                ]);

                Log::info("ManifestPushedListener: Tag created", $tag->toArray());
            }
        });
    }
}
