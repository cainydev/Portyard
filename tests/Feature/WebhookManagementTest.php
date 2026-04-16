<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Enums\WebhookTrigger;
use App\Models\Repository;
use App\Models\User;
use App\Models\Webhook;
use App\Policies\WebhookPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_matches_tag_helper(): void
    {
        $webhook = Webhook::factory()->make(['tag_filter' => 'v*']);
        $this->assertTrue($webhook->matchesTag('v1.0.0'));
        $this->assertFalse($webhook->matchesTag('latest'));

        $webhook->tag_filter = null;
        $this->assertTrue($webhook->matchesTag('anything'));
    }

    public function test_webhook_subscribes_to_helper(): void
    {
        $webhook = Webhook::factory()->make([
            'events' => [WebhookTrigger::TagPushed->value, WebhookTrigger::TagDeleted->value],
        ]);

        $this->assertTrue($webhook->subscribesTo(WebhookTrigger::TagPushed));
        $this->assertTrue($webhook->subscribesTo(WebhookTrigger::TagDeleted));
        $this->assertFalse($webhook->subscribesTo(WebhookTrigger::TagUpdated));
    }

    public function test_secret_is_encrypted_at_rest(): void
    {
        $owner = User::factory()->create();
        $repo = Repository::factory()->for($owner->spaces()->first())->create(['name' => 'demo']);

        $webhook = Webhook::factory()->for($repo)->create(['secret' => 'plain-secret-value']);

        $raw = \DB::table('webhooks')->where('id', $webhook->id)->value('secret');

        $this->assertNotSame('plain-secret-value', $raw);
        $this->assertSame('plain-secret-value', $webhook->fresh()->secret);
    }

    public function test_policy_allows_maintainer_and_denies_viewer(): void
    {
        $owner = User::factory()->create();
        $repo = Repository::factory()->for($owner->spaces()->first())->create(['name' => 'demo']);
        $webhook = Webhook::factory()->for($repo)->create();

        $viewer = User::factory()->create();
        $repo->space->users()->attach($viewer, ['role' => Roles::Viewer->value]);

        $maintainer = User::factory()->create();
        $repo->space->users()->attach($maintainer, ['role' => Roles::Maintainer->value]);

        $policy = new WebhookPolicy;

        $this->assertTrue($policy->create($owner, $repo));
        $this->assertTrue($policy->update($maintainer, $webhook));
        $this->assertFalse($policy->update($viewer, $webhook));
        $this->assertFalse($policy->delete($viewer, $webhook));
    }
}
