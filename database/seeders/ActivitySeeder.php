<?php

namespace Database\Seeders;

use App\Enums\Action;
use App\Models\Activity;
use App\Models\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $spaces = Space::with(['repositories.tags', 'repositories.webhooks'])->get();

        $activities = [];
        $now = Carbon::now();
        $baseDate = $now->copy()->subMonths(3);

        foreach ($spaces as $space) {
            $actors = $space->users;
            $owner = $actors->first();

            // 1. Space Created (Near the base date)
            $spaceDate = $baseDate->copy()->addMinutes(rand(1, 60));
            $activities[] = $this->buildActivityData(
                subject: $space,
                spaceId: $space->id,
                userId: $owner->id,
                action: Action::SpaceCreated->value,
                description: "Created the space {$space->name}",
                date: $spaceDate
            );

            // 2. Members Joined (Within the first 5 days of space creation)
            if ($actors->count() > 1) {
                foreach ($actors as $actor) {
                    if ($actor->id === $owner->id) {
                        continue;
                    }

                    $activities[] = $this->buildActivityData(
                        subject: $actor,
                        spaceId: $space->id,
                        userId: $actor->id,
                        action: Action::MemberAccepted->value,
                        description: "Joined the space {$space->name}",
                        date: $spaceDate->copy()->addHours(rand(1, 120))
                    );
                }
            }

            foreach ($space->repositories as $repository) {
                $repoActor = $actors->random();

                // 3. Repository Created (Anytime between Space creation and 2 weeks ago)
                $maxRepoDays = max(1, $spaceDate->diffInDays($now->copy()->subWeeks(2)));
                $repoDate = $spaceDate->copy()->addDays(rand(1, $maxRepoDays))->addHours(rand(1, 24));
                $requestId = Str::uuid()->toString();

                $activities[] = $this->buildActivityData(
                    subject: $repository,
                    spaceId: $space->id,
                    userId: $repoActor->id,
                    action: Action::RepositoryCreated->value,
                    description: "Created repository {$repository->name}",
                    date: $repoDate,
                    requestId: $requestId
                );

                // 4. Webhooks Created (Anytime between Repo creation and Now)
                foreach ($repository->webhooks as $webhook) {
                    $maxWebhookMinutes = max(1, $repoDate->diffInMinutes($now));
                    $activities[] = $this->buildActivityData(
                        subject: $webhook,
                        spaceId: $space->id,
                        userId: $actors->random()->id,
                        action: Action::WebhookCreated->value,
                        description: "Configured a webhook for {$repository->name}",
                        date: $repoDate->copy()->addMinutes(rand(1, $maxWebhookMinutes))
                    );
                }

                // 5. Tags Pushed (Anytime between Repo creation and Now)
                foreach ($repository->tags as $tag) {
                    $maxTagMinutes = max(1, $repoDate->diffInMinutes($now));
                    $activities[] = $this->buildActivityData(
                        subject: $tag,
                        spaceId: $space->id,
                        userId: $actors->random()->id,
                        action: Action::TagPushed->value,
                        description: "Pushed tag {$tag->name} to {$repository->name}",
                        date: $repoDate->copy()->addMinutes(rand(10, $maxTagMinutes))
                    );
                }
            }
        }

        // Sort all activities chronologically so they are inserted in the exact order they "occurred".
        // This simulates a real database naturally filling up over time.
        $sortedActivities = collect($activities)
            ->sortBy(fn ($activity) => $activity['created_at']->timestamp)
            ->values();

        // Chunk inserts for performance
        $sortedActivities->chunk(500)->each(function ($chunk) {
            Activity::insert($chunk->toArray());
        });
    }

    private function buildActivityData($subject, string $spaceId, string $userId, string $action, string $description, Carbon $date, ?string $requestId = null): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'request_id' => $requestId ?? Str::uuid()->toString(),
            'space_id' => $spaceId,
            'user_id' => $userId,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->id,
            'action' => $action,
            'description' => $description,
            'metadata' => json_encode(['ip' => '127.0.0.1', 'user_agent' => 'Database Seeder']),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
