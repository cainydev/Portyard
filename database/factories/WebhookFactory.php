<?php

namespace Database\Factories;

use App\Enums\WebhookTrigger;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, asText: true),
            'description' => null,
            'url' => $this->faker->url(),
            'secret' => Str::random(48),
            'enabled' => true,
            'events' => [WebhookTrigger::TagPushed->value],
            'tag_filter' => null,
            'template' => 'generic',
        ];
    }
}
