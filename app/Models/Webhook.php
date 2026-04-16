<?php

namespace App\Models;

use App\Enums\WebhookTrigger;
use App\Events\Webhook\WebhookCreated;
use App\Events\Webhook\WebhookDeleted;
use App\Events\Webhook\WebhookUpdated;
use App\Policies\WebhookPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[UsePolicy(WebhookPolicy::class)]
class Webhook extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'repository_id',
        'name',
        'description',
        'url',
        'secret',
        'enabled',
        'events',
        'tag_filter',
        'template',
    ];

    protected $hidden = ['secret'];

    protected $with = ['repository'];

    protected function casts(): array
    {
        return [
            'events' => AsEnumCollection::of(WebhookTrigger::class),
            'enabled' => 'boolean',
            'secret' => 'encrypted',
        ];
    }

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

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function subscribesTo(WebhookTrigger $trigger): bool
    {
        return $this->events?->contains($trigger) ?? false;
    }

    public function matchesTag(?string $tag): bool
    {
        if (blank($this->tag_filter)) {
            return true;
        }

        if (blank($tag)) {
            return false;
        }

        return Str::is($this->tag_filter, $tag);
    }
}
