<?php

namespace App\Support\Webhooks\Templates;

class SlackTemplate implements TemplateTransformer
{
    public function transform(array $payload): array
    {
        $event = $payload['event'] ?? 'event';
        $repo = $payload['repository']['full_name'] ?? 'repository';
        $tag = $payload['tag']['name'] ?? null;
        $size = $this->formatBytes((int) ($payload['tag']['size_bytes'] ?? 0));
        $actor = $payload['pusher']['name'] ?? null;

        $title = match ($event) {
            'tag_pushed' => ":package: `{$repo}:{$tag}` pushed",
            'tag_updated' => ":arrows_counterclockwise: `{$repo}:{$tag}` updated",
            'tag_deleted' => ":wastebasket: `{$repo}:{$tag}` deleted",
            'ping' => ':wave: Test ping from Portyard',
            default => ":bell: {$event} · `{$repo}`",
        };

        $fields = [];

        if ($tag) {
            $fields[] = ['type' => 'mrkdwn', 'text' => "*Tag*\n`{$tag}`"];
        }

        if ($size && $event !== 'tag_deleted') {
            $fields[] = ['type' => 'mrkdwn', 'text' => "*Size*\n{$size}"];
        }

        if ($actor) {
            $fields[] = ['type' => 'mrkdwn', 'text' => "*Pusher*\n{$actor}"];
        }

        $blocks = [
            [
                'type' => 'section',
                'text' => ['type' => 'mrkdwn', 'text' => $title],
            ],
        ];

        if (! empty($fields)) {
            $blocks[] = ['type' => 'section', 'fields' => $fields];
        }

        return [
            'text' => $title,
            'blocks' => $blocks,
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
