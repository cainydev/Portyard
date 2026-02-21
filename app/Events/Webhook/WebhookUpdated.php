<?php

namespace App\Events\Webhook;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookUpdated implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Webhook $webhook,
        public ?User $actor = null
    ) {}

    public function action(): Action
    {
        return Action::WebhookUpdated;
    }

    public function subject(): Webhook
    {
        return $this->webhook;
    }

    public function spaceId(): string
    {
        return $this->webhook->repository->space_id;
    }

    public function properties(): array
    {
        return [
            'url' => $this->webhook->url,
            'repository' => $this->webhook->repository->name,
        ];
    }
}
