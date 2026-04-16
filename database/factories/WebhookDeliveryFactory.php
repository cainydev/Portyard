<?php

namespace Database\Factories;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebhookDelivery>
 */
class WebhookDeliveryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'webhook_id' => Webhook::factory(),
            'event' => 'tag_pushed',
            'status' => WebhookDelivery::STATUS_SUCCESS,
            'attempt' => 1,
            'request_headers' => ['Content-Type' => 'application/json'],
            'request_body' => ['event' => 'tag_pushed'],
            'response_status' => 200,
            'response_headers' => ['content-type' => 'application/json'],
            'response_body' => '{"ok":true}',
            'duration_ms' => 42,
            'delivered_at' => now(),
        ];
    }
}
