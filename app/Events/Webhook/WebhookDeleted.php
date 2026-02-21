<?php

namespace App\Events\Webhook;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookDeleted implements TrackableEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Webhook $webhook,
        public ?User $actor = null
    ) {}

    public function action(): Action
    {
        return Action::WebhookDeleted;
    }

    public function subject(): null
    {
        return null;
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
