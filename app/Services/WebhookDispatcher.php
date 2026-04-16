<?php

namespace App\Services;

use App\Enums\WebhookTrigger;
use App\Jobs\DeliverWebhookJob;
use App\Models\Repository;
use App\Models\User;
use App\Models\Webhook;
use App\Support\Webhooks\PayloadBuilder;
use App\Support\Webhooks\Templates\DiscordTemplate;
use App\Support\Webhooks\Templates\SlackTemplate;
use App\Support\Webhooks\Templates\TemplateTransformer;

class WebhookDispatcher
{
    /**
     * Dispatch a trigger to every matching webhook on the repository.
     *
     * @param  array{tag?: mixed, tag_name?: ?string, manifest?: mixed, actor?: ?User}  $context
     */
    public function dispatch(Repository $repository, WebhookTrigger $trigger, array $context = []): void
    {
        $tagName = $context['tag_name'] ?? ($context['tag']->name ?? null);

        $webhooks = $repository->webhooks()
            ->where('enabled', true)
            ->get()
            ->filter(fn (Webhook $webhook) => $webhook->subscribesTo($trigger) && $webhook->matchesTag($tagName));

        foreach ($webhooks as $webhook) {
            $payload = PayloadBuilder::build($webhook, $trigger, $repository, $context);
            $payload = $this->applyTemplate($webhook->template, $payload);

            DeliverWebhookJob::dispatch($webhook->id, $trigger->value, $payload);
        }
    }

    /**
     * Dispatch a synthetic "ping" delivery so the user can verify the receiver.
     */
    public function ping(Webhook $webhook, ?User $actor = null): void
    {
        $payload = PayloadBuilder::ping($webhook, $webhook->repository, $actor);
        $payload = $this->applyTemplate($webhook->template, $payload);

        DeliverWebhookJob::dispatch($webhook->id, 'ping', $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function applyTemplate(?string $template, array $payload): array
    {
        $transformer = $this->transformer($template);

        return $transformer ? $transformer->transform($payload) : $payload;
    }

    protected function transformer(?string $template): ?TemplateTransformer
    {
        return match ($template) {
            'slack' => new SlackTemplate,
            'discord' => new DiscordTemplate,
            default => null,
        };
    }
}
