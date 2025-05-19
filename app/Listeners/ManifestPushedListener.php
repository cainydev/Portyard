<?php

namespace App\Listeners;

use Cainy\Dockhand\Events\ManifestPushedEvent;

class ManifestPushedListener
{
    public function handle(ManifestPushedEvent $event): void
    {
        // TODO: Implement the logic to handle the ManifestPushedEvent
        /*Log::channel('dockhand_listener')->info("Handling ManifestPushedEvent", [
            'repository' => $event->targetRepository,
            'tag' => $event->getTargetTag(),
            'digest' => $event->getTargetDigest(),
            'actor' => $event->getActorName(),
        ]);

        DB::transaction(function () use ($event) {
            // 1. Resolve User from actorName
            // This is a simplified approach. You might have more complex logic.
            $user = User::where('username', $event->getActorName())->first();
            // if (!$user && config('dockhand_app.fallback_user_id')) {
            //     $user = User::find(config('dockhand_app.fallback_user_id'));
            // }

            // 2. Find or Create Repository
            $repository = Repository::firstOrCreate(
                ['name' => $event->getTargetRepository()],
                [
                    'user_id' => $user?->id, // Assign owner if user resolved
                    'last_pushed_at' => $event->getTimestamp(),
                    // Add other default fields for new repository if needed
                ]
            );

            // Update last_pushed_at even if repository existed
            $repository->last_pushed_at = $event->getTimestamp();
            if ($repository->isDirty('user_id') && !$repository->user_id && $user) {
                // If repo existed but had no owner, and we found one now
                $repository->user_id = $user->id;
            }
            $repository->save();

            // 3. Create or Update Tag
            // A push event usually has a tag.
            if ($event->getTargetTag()) {
                Tag::updateOrCreate(
                    [
                        'repository_id' => $repository->id,
                        'name' => $event->getTargetTag(),
                    ],
                    [
                        'manifest_digest' => $event->getTargetDigest(),
                        'size' => $event->getTargetSize(),
                        'pushed_at' => $event->getTimestamp(),
                        'pulled_at' => null, // Reset pulled_at if re-pushed
                        'pull_count' => 0,   // Reset pull_count if re-pushed
                    ]
                );
            } else {
                // Manifest pushed without a tag (e.g., by digest only)
                // You might want to record this manifest somewhere if you track untagged manifests.
                // For now, we're only creating/updating tags.
                Log::channel('dockhand_listener')->info("Manifest pushed without a tag.", ['digest' => $event->getTargetDigest()]);
            }

            // 4. (Optional) Update repository_user pivot table if a user was resolved
            if ($user && !$repository->users()->where('users.id', $user->id)->exists()) {
                // Assuming a default role like 'owner' on first push by user
                // $repository->users()->attach($user->id, ['role' => 'owner']);
            }

        });*/
    }
}
