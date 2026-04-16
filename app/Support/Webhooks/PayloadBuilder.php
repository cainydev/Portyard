<?php

namespace App\Support\Webhooks;

use App\Enums\WebhookTrigger;
use App\Models\Manifest;
use App\Models\Repository;
use App\Models\Tag;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Carbon;

class PayloadBuilder
{
    /**
     * Build the generic payload for a webhook event.
     *
     * @param  array{tag?: ?Tag, tag_name?: ?string, manifest?: ?Manifest, actor?: ?User}  $context
     * @return array<string, mixed>
     */
    public static function build(
        Webhook $webhook,
        WebhookTrigger $trigger,
        Repository $repository,
        array $context = [],
    ): array {
        $now = Carbon::now()->toIso8601String();

        $payload = [
            'event' => $trigger->value,
            'delivered_at' => $now,
            'webhook' => [
                'id' => $webhook->id,
                'name' => $webhook->name,
            ],
            'repository' => self::repository($repository),
            'tag' => self::tag($context),
        ];

        $actor = $context['actor'] ?? null;

        if ($trigger === WebhookTrigger::TagDeleted) {
            $payload['deleted_at'] = $now;
        } else {
            $payload['pusher'] = $actor instanceof User ? self::user($actor) : null;
            $tag = $context['tag'] ?? null;
            $payload['pushed_at'] = ($tag instanceof Tag && $tag->last_pushed) ? $tag->last_pushed->toIso8601String() : $now;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function ping(Webhook $webhook, Repository $repository, ?User $actor): array
    {
        return [
            'event' => 'ping',
            'delivered_at' => Carbon::now()->toIso8601String(),
            'webhook' => [
                'id' => $webhook->id,
                'name' => $webhook->name,
            ],
            'repository' => self::repository($repository),
            'actor' => $actor ? self::user($actor) : null,
            'message' => 'Test delivery from Portyard.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function repository(Repository $repository): array
    {
        return [
            'id' => $repository->id,
            'name' => $repository->name,
            'namespace' => $repository->space->namespace,
            'full_name' => $repository->path,
            'public' => (bool) $repository->public,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|null
     */
    protected static function tag(array $context): ?array
    {
        $tag = $context['tag'] ?? null;
        $tagName = $context['tag_name'] ?? $tag?->name;

        if (! $tagName) {
            return null;
        }

        $manifest = $context['manifest'] ?? $tag?->manifest;

        return [
            'name' => $tagName,
            'digest' => $manifest?->digest,
            'media_type' => $manifest?->media_type?->toString(),
            'size_bytes' => (int) ($manifest?->size_bytes ?? 0),
            'platforms' => self::platforms($manifest),
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    protected static function platforms(?Manifest $manifest): array
    {
        if (! $manifest) {
            return [];
        }

        if ($manifest->isManifestList()) {
            return $manifest->childManifestEntries()
                ->get()
                ->map(fn ($entry) => [
                    'os' => $entry->platform_os,
                    'architecture' => $entry->platform_architecture,
                    'variant' => $entry->platform_variant,
                ])
                ->all();
        }

        $config = $manifest->imageConfig;

        if (! $config) {
            return [];
        }

        return [[
            'os' => $config->os,
            'architecture' => $config->architecture,
            'variant' => $config->variant,
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'slug' => $user->slug ?? null,
            'email' => $user->email,
        ];
    }
}
