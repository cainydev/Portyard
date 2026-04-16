<?php

namespace App\Support\Webhooks\Templates;

class DiscordTemplate implements TemplateTransformer
{
    public function transform(array $payload): array
    {
        $event = $payload['event'] ?? 'event';
        $repo = $payload['repository']['full_name'] ?? 'repository';
        $tag = $payload['tag']['name'] ?? null;
        $size = $this->formatBytes((int) ($payload['tag']['size_bytes'] ?? 0));
        $actor = $payload['pusher']['name'] ?? null;

        [$title, $color] = match ($event) {
            'tag_pushed' => ["📦 `{$repo}:{$tag}` pushed", 0x22C55E],
            'tag_updated' => ["🔄 `{$repo}:{$tag}` updated", 0x3B82F6],
            'tag_deleted' => ["🗑️ `{$repo}:{$tag}` deleted", 0xEF4444],
            'ping' => ['👋 Test ping from Portyard', 0x6366F1],
            default => ["🔔 {$event} · `{$repo}`", 0x6B7280],
        };

        $fields = [];

        if ($tag) {
            $fields[] = ['name' => 'Tag', 'value' => "`{$tag}`", 'inline' => true];
        }

        if ($size && $event !== 'tag_deleted') {
            $fields[] = ['name' => 'Size', 'value' => $size, 'inline' => true];
        }

        if ($actor) {
            $fields[] = ['name' => 'Pusher', 'value' => $actor, 'inline' => true];
        }

        $embed = array_filter([
            'title' => $title,
            'color' => $color,
            'fields' => $fields ?: null,
            'timestamp' => $payload['delivered_at'] ?? null,
        ], fn ($v) => $v !== null);

        return [
            'embeds' => [$embed],
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
