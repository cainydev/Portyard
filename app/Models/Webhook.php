<?php

namespace App\Models;

use App\Enums\WebhookTrigger;
use App\Events\Webhook\WebhookCreated;
use App\Events\Webhook\WebhookDeleted;
use App\Events\Webhook\WebhookUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'trigger' => WebhookTrigger::class,
    ];

    protected $with = ['repository'];

    protected static function booted(): void
    {
        static::created(function (Webhook $webhook) {
            WebhookCreated::dispatch($webhook, auth()->user());
        });

        static::updated(function (Webhook $webhook) {
            WebhookUpdated::dispatch($webhook, auth()->user());
        });

        static::deleted(function (Webhook $webhook) {
            WebhookDeleted::dispatch($webhook, auth()->user());
        });
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
}
