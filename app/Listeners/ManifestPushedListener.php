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

        $user = User::where('email', $event->actorName)->firstOrFail();

        $repo = Repository::fromPath($event->targetRepository);

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
        });
    }
}
