<?php

namespace Tests\Feature;

use App\Enums\WebhookTrigger;
use App\Jobs\DeliverWebhookJob;
use App\Models\Repository;
use App\Models\User;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\WebhookDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRepository(): Repository
    {
        $user = User::factory()->create();
        $space = $user->spaces()->first();

        return Repository::factory()->for($space)->create(['name' => 'demo']);
    }

    public function test_dispatcher_skips_disabled_webhooks(): void
    {
        Queue::fake();

        $repo = $this->makeRepository();

        Webhook::factory()->for($repo)->create([
            'enabled' => false,
            'events' => [WebhookTrigger::TagPushed->value],
        ]);

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'v1.0.0',
        ]);

        Queue::assertNothingPushed();
    }

    public function test_dispatcher_skips_webhooks_not_subscribed_to_trigger(): void
    {
        Queue::fake();

        $repo = $this->makeRepository();

        Webhook::factory()->for($repo)->create([
            'events' => [WebhookTrigger::TagDeleted->value],
        ]);

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'v1.0.0',
        ]);

        Queue::assertNothingPushed();
    }

    public function test_dispatcher_applies_tag_glob_filter(): void
    {
        Queue::fake();

        $repo = $this->makeRepository();

        Webhook::factory()->for($repo)->create([
            'events' => [WebhookTrigger::TagPushed->value],
            'tag_filter' => 'v*',
        ]);

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'latest',
        ]);
        Queue::assertNothingPushed();

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'v1.2.3',
        ]);
        Queue::assertPushed(DeliverWebhookJob::class, 1);
    }

    public function test_delivery_records_success_and_signs_payload(): void
    {
        Http::fake([
            '*' => Http::response('{"ok":true}', 200, ['Content-Type' => 'application/json']),
        ]);

        $repo = $this->makeRepository();
        $webhook = Webhook::factory()->for($repo)->create([
            'secret' => 'test-secret-abc',
            'events' => [WebhookTrigger::TagPushed->value],
        ]);

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'v1.0.0',
            'actor' => $repo->space->users()->first(),
        ]);

        $delivery = WebhookDelivery::where('webhook_id', $webhook->id)->firstOrFail();
        $this->assertSame(WebhookDelivery::STATUS_SUCCESS, $delivery->status);
        $this->assertSame(200, $delivery->response_status);

        Http::assertSent(function (HttpRequest $request) use ($webhook) {
            $expected = 'sha256='.hash_hmac('sha256', $request->body(), 'test-secret-abc');

            return $request->url() === $webhook->url
                && $request->header('X-Portyard-Event') === ['tag_pushed']
                && $request->header('X-Portyard-Signature-256') === [$expected];
        });
    }

    public function test_delivery_marks_failed_on_non_2xx_response(): void
    {
        Http::fake([
            '*' => Http::response('server error', 500),
        ]);

        $repo = $this->makeRepository();
        $webhook = Webhook::factory()->for($repo)->create([
            'events' => [WebhookTrigger::TagPushed->value],
        ]);

        try {
            app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
                'tag_name' => 'v1.0.0',
            ]);
        } catch (\Throwable) {
            // DeliverWebhookJob throws on failure so the queue can retry;
            // with sync driver the exception surfaces here.
        }

        $delivery = WebhookDelivery::where('webhook_id', $webhook->id)->firstOrFail();
        $this->assertSame(WebhookDelivery::STATUS_FAILED, $delivery->status);
        $this->assertSame(500, $delivery->response_status);
    }

    public function test_ping_creates_delivery_with_ping_event(): void
    {
        Http::fake(['*' => Http::response('', 204)]);

        $repo = $this->makeRepository();
        $webhook = Webhook::factory()->for($repo)->create();

        app(WebhookDispatcher::class)->ping($webhook, $repo->space->users()->first());

        $delivery = WebhookDelivery::where('webhook_id', $webhook->id)->firstOrFail();
        $this->assertSame('ping', $delivery->event);
        $this->assertSame(WebhookDelivery::STATUS_SUCCESS, $delivery->status);
    }

    public function test_redeliver_creates_fresh_delivery_row(): void
    {
        Http::fake(['*' => Http::response('{}', 200)]);

        $repo = $this->makeRepository();
        $webhook = Webhook::factory()->for($repo)->create();

        $original = WebhookDelivery::factory()->for($webhook)->create([
            'status' => WebhookDelivery::STATUS_FAILED,
            'request_body' => ['event' => 'tag_pushed', 'repository' => ['full_name' => $repo->path]],
            'response_status' => 500,
        ]);

        DeliverWebhookJob::redeliver($original);

        $this->assertSame(2, $webhook->deliveries()->count());
        $this->assertDatabaseHas('webhook_deliveries', [
            'id' => $original->id,
            'status' => WebhookDelivery::STATUS_FAILED,
        ]);
    }

    public function test_slack_template_transforms_payload(): void
    {
        Http::fake(['*' => Http::response('ok', 200)]);

        $repo = $this->makeRepository();
        Webhook::factory()->for($repo)->create([
            'template' => 'slack',
            'events' => [WebhookTrigger::TagPushed->value],
        ]);

        app(WebhookDispatcher::class)->dispatch($repo, WebhookTrigger::TagPushed, [
            'tag_name' => 'v1.0.0',
            'actor' => $repo->space->users()->first(),
        ]);

        Http::assertSent(function (HttpRequest $request) {
            $body = json_decode($request->body(), true);

            return isset($body['text'], $body['blocks']);
        });
    }
}
