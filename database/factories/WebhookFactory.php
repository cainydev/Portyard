<?php

namespace Database\Factories;

use App\Enums\WebhookTrigger;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trigger' => $this->faker->randomElement(WebhookTrigger::values()),
            'name' => $this->faker->words(asText: true),
            'url' => $this->faker->url(),
        ];
    }
}
