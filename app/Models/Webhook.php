<?php

namespace App\Models;

use App\Enums\WebhookTrigger;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'trigger' => WebhookTrigger::class,
    ];

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
}
