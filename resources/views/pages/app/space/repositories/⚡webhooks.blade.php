<?php

use App\Enums\WebhookTrigger;
use App\Models\Repository;
use App\Models\Webhook;
use App\Rules\SafeWebhookUrl;
use App\Services\WebhookDispatcher;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Repository $repository;

    public ?string $editingId = null;

    public string $name = '';

    public ?string $description = null;

    public string $url = '';

    public array $events = [];

    public ?string $tag_filter = null;

    public string $template = 'generic';

    public bool $enabled = true;

    public ?string $generatedSecret = null;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
    }

    #[Computed]
    public function webhooks()
    {
        return $this->repository->webhooks()
            ->withCount(['deliveries as deliveries_total'])
            ->withCount(['deliveries as deliveries_failed' => fn ($q) => $q->where('status', 'failed')])
            ->latest()
            ->get();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'url' => ['required', 'url:http,https', new SafeWebhookUrl],
            'events' => 'required|array|min:1',
            'events.*' => ['required', Rule::enum(WebhookTrigger::class)],
            'tag_filter' => 'nullable|string|max:100',
            'template' => 'required|in:generic,slack,discord',
            'enabled' => 'boolean',
        ];
    }

    public function openCreate(): void
    {
        $this->authorize('create', [Webhook::class, $this->repository]);

        $this->resetForm();
        \Flux\Flux::modal('webhook-form')->show();
    }

    public function openEdit(string $id): void
    {
        $webhook = $this->repository->webhooks()->findOrFail($id);
        $this->authorize('update', $webhook);

        $this->editingId = $webhook->id;
        $this->name = $webhook->name;
        $this->description = $webhook->description;
        $this->url = $webhook->url;
        $this->events = $webhook->events?->map(fn ($e) => $e->value)->all() ?? [];
        $this->tag_filter = $webhook->tag_filter;
        $this->template = $webhook->template ?? 'generic';
        $this->enabled = (bool) $webhook->enabled;
        $this->generatedSecret = null;

        \Flux\Flux::modal('webhook-form')->show();
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $webhook = $this->repository->webhooks()->findOrFail($this->editingId);
            $this->authorize('update', $webhook);
            $webhook->update($data);
            \Flux\Flux::toast(__('Webhook updated.'), __('Saved'), 2000, 'success');
        } else {
            $this->authorize('create', [Webhook::class, $this->repository]);
            $this->generatedSecret = Str::random(48);
            $this->repository->webhooks()->create($data + ['secret' => $this->generatedSecret]);
            \Flux\Flux::toast(__('Webhook created.'), __('Saved'), 2000, 'success');
        }

        unset($this->webhooks);
        \Flux\Flux::modal('webhook-form')->close();

        if ($this->generatedSecret) {
            \Flux\Flux::modal('webhook-secret')->show();
        }
    }

    public function toggle(string $id): void
    {
        $webhook = $this->repository->webhooks()->findOrFail($id);
        $this->authorize('update', $webhook);
        $webhook->update(['enabled' => ! $webhook->enabled]);
        unset($this->webhooks);
    }

    public function testPing(string $id): void
    {
        $webhook = $this->repository->webhooks()->findOrFail($id);
        $this->authorize('update', $webhook);

        app(WebhookDispatcher::class)->ping($webhook, auth()->user());
        \Flux\Flux::toast(__('Test ping queued.'), __('Sent'), 2000, 'success');
    }

    public function delete(string $id): void
    {
        $webhook = $this->repository->webhooks()->findOrFail($id);
        $this->authorize('delete', $webhook);
        $webhook->delete();
        unset($this->webhooks);
        \Flux\Flux::toast(__('Webhook deleted.'), __('Deleted'), 2000, 'success');
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = null;
        $this->url = '';
        $this->events = [WebhookTrigger::TagPushed->value];
        $this->tag_filter = null;
        $this->template = 'generic';
        $this->enabled = true;
        $this->generatedSecret = null;
        $this->resetValidation();
    }

    public function render()
    {
        return $this->view()
            ->title(__('Webhooks').' · '."{$this->repository->path}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container class="p-0">
        <x-app.section-header
            class="p-6 lg:p-8"
            :title="__('Webhooks')"
            :subtitle="__('Send signed HTTP requests to external services when tags change in this repository.')">
            @if(auth()->user()->can('create', [App\Models\Webhook::class, $repository]))
                <flux:button variant="primary" icon="plus" wire:click="openCreate">
                    {{ __('New Webhook') }}
                </flux:button>
            @endif
        </x-app.section-header>

        <div class="border-t border-stitched">
            <flux:table class="border-stitched">
                <flux:table.columns>
                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">{{ __('Name') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('URL') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('Events') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('Template') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->webhooks as $webhook)
                        <flux:table.row class="hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20">
                            <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                <div class="flex flex-col">
                                    <flux:text>{{ $webhook->name }}</flux:text>
                                    @if($webhook->description)
                                        <flux:text variant="subtle" size="sm">{{ $webhook->description }}</flux:text>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text variant="subtle" class="font-mono text-xs">
                                    {{ parse_url($webhook->url, PHP_URL_HOST) ?: $webhook->url }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($webhook->events ?? [] as $event)
                                        <flux:badge size="sm" color="zinc">{{ $event->getReadableName() }}</flux:badge>
                                    @endforeach
                                    @if($webhook->tag_filter)
                                        <flux:badge size="sm" color="blue" icon="funnel">{{ $webhook->tag_filter }}</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:badge size="sm" color="{{ match($webhook->template) { 'slack' => 'purple', 'discord' => 'indigo', default => 'zinc' } }}">
                                    {{ ucfirst($webhook->template) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <div class="flex items-center gap-2 h-full">
                                    @if($webhook->enabled)
                                        <span class="size-2 rounded-full bg-green-500"></span>
                                        <flux:text size="sm">{{ __('Enabled') }}</flux:text>
                                    @else
                                        <span class="size-2 rounded-full bg-zinc-400"></span>
                                        <flux:text size="sm" variant="subtle">{{ __('Disabled') }}</flux:text>
                                    @endif
                                    @if($webhook->deliveries_failed > 0)
                                        <flux:badge size="sm" color="red">{{ $webhook->deliveries_failed }} {{ __('failed') }}</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched w-px last:pe-6 lg:last:pe-8">
                                @if(auth()->user()->can('update', $webhook))
                                    <flux:dropdown align="end">
                                        <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal"/>
                                        <flux:menu>
                                            <flux:menu.item
                                                icon="arrow-top-right-on-square"
                                                :href="route('app.space.repositories.webhooks.show', [$repository->space, $repository, $webhook])"
                                                wire:navigate.hover>
                                                {{ __('View deliveries') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="paper-airplane" wire:click="testPing('{{ $webhook->id }}')">
                                                {{ __('Send test ping') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="pencil-square" wire:click="openEdit('{{ $webhook->id }}')">
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                            <flux:menu.item icon="power" wire:click="toggle('{{ $webhook->id }}')">
                                                {{ $webhook->enabled ? __('Disable') : __('Enable') }}
                                            </flux:menu.item>
                                            <flux:menu.separator/>
                                            <flux:menu.item
                                                icon="trash"
                                                variant="danger"
                                                wire:click="delete('{{ $webhook->id }}')"
                                                wire:confirm="{{ __('Delete this webhook? This cannot be undone.') }}">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="first:ps-6 lg:first:ps-8 last:pe-6 lg:last:pe-8">
                                <flux:text variant="subtle">
                                    {{ __('No webhooks yet. Create one to be notified when tags are pushed, updated, or deleted.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-container>

    {{-- Create / Edit modal --}}
    <flux:modal name="webhook-form" class="min-w-[28rem] max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? __('Edit webhook') : __('New webhook') }}
                </flux:heading>
                <flux:text class="mt-1">
                    {{ __('Portyard will POST a signed JSON payload to your URL.') }}
                </flux:text>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="Production CI"/>
                <flux:error name="name"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:input wire:model="description" :placeholder="__('Optional. What does this webhook do?')"/>
                <flux:error name="description"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Payload URL') }}</flux:label>
                <flux:input wire:model="url" type="url" placeholder="https://example.com/webhooks/portyard"/>
                <flux:error name="url"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Events') }}</flux:label>
                <flux:description>{{ __('Which triggers should fire this webhook?') }}</flux:description>
                <div class="space-y-2 mt-2">
                    @foreach(WebhookTrigger::cases() as $case)
                        <flux:checkbox wire:model="events" value="{{ $case->value }}" :label="$case->getReadableName()"/>
                    @endforeach
                </div>
                <flux:error name="events"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Tag filter') }}</flux:label>
                <flux:input wire:model="tag_filter" placeholder="v*"/>
                <flux:description>
                    {{ __('Optional glob pattern. Only tags matching this will fire the webhook. Examples: v*, latest, release-*') }}
                </flux:description>
                <flux:error name="tag_filter"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Payload template') }}</flux:label>
                <flux:select wire:model="template">
                    <flux:select.option value="generic">{{ __('Generic (default JSON)') }}</flux:select.option>
                    <flux:select.option value="slack">{{ __('Slack incoming webhook') }}</flux:select.option>
                    <flux:select.option value="discord">{{ __('Discord webhook') }}</flux:select.option>
                </flux:select>
                <flux:description>
                    {{ __('Pick Slack or Discord to get native notifications in those apps. Generic sends full JSON for custom receivers.') }}
                </flux:description>
                <flux:error name="template"/>
            </flux:field>

            <flux:field variant="inline">
                <flux:switch wire:model="enabled"/>
                <flux:label>{{ __('Enabled') }}</flux:label>
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="save">
                    {{ $editingId ? __('Save changes') : __('Create webhook') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Secret reveal modal (one-time on create) --}}
    <flux:modal name="webhook-secret" class="min-w-[28rem] max-w-2xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Copy your signing secret') }}</flux:heading>
            <flux:text>
                {{ __('Portyard signs every delivery with HMAC-SHA256 using this secret. We will not show it again.') }}
            </flux:text>
            <flux:input readonly copyable value="{{ $generatedSecret }}"/>
            <flux:callout icon="shield-check" color="blue">
                <flux:callout.heading>{{ __('Verify deliveries in your receiver') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Compare the X-Portyard-Signature-256 header with hmac_sha256(secret, raw_body).') }}
                </flux:callout.text>
            </flux:callout>
            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="primary">{{ __("I've copied it") }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</x-layouts.repository>
