<?php

use App\Jobs\DeliverWebhookJob;
use App\Models\Repository;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public Repository $repository;

    public Webhook $webhook;

    public ?string $inspectingId = null;

    public ?string $rotatedSecret = null;

    public function mount(Repository $repository, Webhook $webhook): void
    {
        abort_unless($webhook->repository_id === $repository->id, 404);

        $this->authorize('view', $webhook);

        $this->repository = $repository;
        $this->webhook = $webhook;
    }

    #[Computed]
    public function deliveries(): LengthAwarePaginator
    {
        return $this->webhook->deliveries()
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function hasPending(): bool
    {
        return $this->webhook->deliveries()->where('status', 'pending')->exists();
    }

    public function inspect(string $id): void
    {
        $this->inspectingId = $id;
        \Flux\Flux::modal('delivery-inspector')->show();
    }

    #[Computed]
    public function inspectedDelivery(): ?WebhookDelivery
    {
        if (! $this->inspectingId) {
            return null;
        }

        return $this->webhook->deliveries()->find($this->inspectingId);
    }

    public function redeliver(string $id): void
    {
        $this->authorize('update', $this->webhook);

        $delivery = $this->webhook->deliveries()->findOrFail($id);
        DeliverWebhookJob::redeliver($delivery);

        unset($this->deliveries);
        \Flux\Flux::toast(__('Redelivery queued.'), __('Sent'), 2000, 'success');
    }

    public function rotateSecret(): void
    {
        $this->authorize('update', $this->webhook);

        $this->rotatedSecret = Str::random(48);
        $this->webhook->update(['secret' => $this->rotatedSecret]);

        \Flux\Flux::modal('rotated-secret')->show();
    }

    public function render()
    {
        return $this->view()
            ->title(__('Deliveries').' · '."{$this->webhook->name}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container class="p-0">
        <x-app.section-header
            class="p-6 lg:p-8"
            :title="$webhook->name"
            :subtitle="$webhook->description ?: $webhook->url">
            <flux:button
                :href="route('app.space.repositories.webhooks', [$repository->space, $repository])"
                icon="arrow-left"
                variant="ghost"
                size="sm"
                wire:navigate.hover>
                {{ __('Back') }}
            </flux:button>
            @if(auth()->user()->can('update', $webhook))
                <flux:button
                    wire:click="rotateSecret"
                    wire:confirm="{{ __('Rotate the signing secret? Existing receivers will need the new secret to verify.') }}"
                    icon="arrow-path"
                    variant="outline"
                    size="sm">
                    {{ __('Rotate secret') }}
                </flux:button>
            @endif
        </x-app.section-header>

        <div class="border-t border-stitched grid grid-cols-1 sm:grid-cols-3">
            <div class="p-6 lg:p-8 sm:border-r border-stitched">
                <flux:text variant="subtle" size="sm">{{ __('URL') }}</flux:text>
                <flux:text class="font-mono text-xs break-all mt-1">{{ $webhook->url }}</flux:text>
            </div>
            <div class="p-6 lg:p-8 sm:border-r border-stitched border-t sm:border-t-0">
                <flux:text variant="subtle" size="sm">{{ __('Events') }}</flux:text>
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($webhook->events ?? [] as $event)
                        <flux:badge size="sm" color="zinc">{{ $event->getReadableName() }}</flux:badge>
                    @endforeach
                </div>
            </div>
            <div class="p-6 lg:p-8 border-stitched border-t sm:border-t-0">
                <flux:text variant="subtle" size="sm">{{ __('Status') }}</flux:text>
                <div class="flex items-center gap-2 mt-2">
                    <span class="size-2 rounded-full {{ $webhook->enabled ? 'bg-green-500' : 'bg-zinc-400' }}"></span>
                    <flux:text size="sm">{{ $webhook->enabled ? __('Enabled') : __('Disabled') }}</flux:text>
                </div>
            </div>
        </div>
    </x-container>

    <x-container class="p-0">
        <x-app.section-header
            class="p-6 lg:p-8"
            :title="__('Recent deliveries')"
            :subtitle="__('Click any row to inspect the request and response.')"/>

        <div class="border-t border-stitched" @if($this->hasPending) wire:poll.5s @endif>
            <flux:table class="border-stitched">
                <flux:table.columns>
                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('Event') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('HTTP') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('Duration') }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __('When') }}</flux:table.column>
                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->deliveries as $delivery)
                        <flux:table.row class="cursor-pointer hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20" wire:click="inspect('{{ $delivery->id }}')">
                            <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                @if($delivery->status === 'success')
                                    <flux:badge size="sm" color="green" icon="check">{{ __('Success') }}</flux:badge>
                                @elseif($delivery->status === 'pending')
                                    <flux:badge size="sm" color="yellow" icon="clock">{{ __('Pending') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red" icon="x-mark">{{ __('Failed') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text class="font-mono text-xs">{{ $delivery->event }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text variant="subtle" size="sm">
                                    {{ $delivery->response_status ?? '—' }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text variant="subtle" size="sm">
                                    {{ $delivery->duration_ms ? $delivery->duration_ms . ' ms' : '—' }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text variant="subtle" size="sm">
                                    {{ $delivery->created_at->diffForHumans() }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched w-px last:pe-6 lg:last:pe-8">
                                @if(auth()->user()->can('update', $webhook))
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="arrow-path"
                                        wire:click.stop="redeliver('{{ $delivery->id }}')">
                                        {{ __('Redeliver') }}
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="first:ps-6 lg:first:ps-8 last:pe-6 lg:last:pe-8">
                                <flux:text variant="subtle">
                                    {{ __('No deliveries yet. Push a tag or send a test ping to see deliveries here.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if($this->deliveries->hasPages())
            <div class="p-6 lg:p-8 border-t border-stitched">
                {{ $this->deliveries->links() }}
            </div>
        @endif
    </x-container>

    {{-- Delivery inspector --}}
    <flux:modal name="delivery-inspector" class="min-w-[32rem] max-w-4xl">
        @if($this->inspectedDelivery)
            @php($d = $this->inspectedDelivery)
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ __('Delivery') }} {{ Str::limit($d->id, 8, '') }}</flux:heading>
                    <flux:text variant="subtle" size="sm" class="font-mono">{{ $d->id }}</flux:text>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div>
                        <flux:text variant="subtle" size="sm">{{ __('Status') }}</flux:text>
                        <flux:text>{{ ucfirst($d->status) }}</flux:text>
                    </div>
                    <div>
                        <flux:text variant="subtle" size="sm">{{ __('HTTP') }}</flux:text>
                        <flux:text>{{ $d->response_status ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text variant="subtle" size="sm">{{ __('Duration') }}</flux:text>
                        <flux:text>{{ $d->duration_ms ? $d->duration_ms . ' ms' : '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text variant="subtle" size="sm">{{ __('Attempt') }}</flux:text>
                        <flux:text>{{ $d->attempt }}</flux:text>
                    </div>
                </div>

                <flux:tab.group>
                    <flux:tabs>
                        <flux:tab name="request">{{ __('Request') }}</flux:tab>
                        <flux:tab name="response">{{ __('Response') }}</flux:tab>
                        @if($d->error)
                            <flux:tab name="error">{{ __('Error') }}</flux:tab>
                        @endif
                    </flux:tabs>

                    <flux:tab.panel name="request">
                        <div class="space-y-3">
                            <div>
                                <flux:text variant="subtle" size="sm" class="mb-1">{{ __('Headers') }}</flux:text>
                                <pre class="bg-zinc-50 dark:bg-zinc-900 rounded p-3 text-xs overflow-x-auto max-h-48">{{ json_encode($d->request_headers, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            <div>
                                <flux:text variant="subtle" size="sm" class="mb-1">{{ __('Body') }}</flux:text>
                                <pre class="bg-zinc-50 dark:bg-zinc-900 rounded p-3 text-xs overflow-x-auto max-h-96">{{ json_encode($d->request_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="response">
                        <div class="space-y-3">
                            <div>
                                <flux:text variant="subtle" size="sm" class="mb-1">{{ __('Headers') }}</flux:text>
                                <pre class="bg-zinc-50 dark:bg-zinc-900 rounded p-3 text-xs overflow-x-auto max-h-48">{{ $d->response_headers ? json_encode($d->response_headers, JSON_PRETTY_PRINT) : '—' }}</pre>
                            </div>
                            <div>
                                <flux:text variant="subtle" size="sm" class="mb-1">{{ __('Body') }}</flux:text>
                                <pre class="bg-zinc-50 dark:bg-zinc-900 rounded p-3 text-xs overflow-x-auto max-h-96">{{ $d->response_body ?: '—' }}</pre>
                            </div>
                        </div>
                    </flux:tab.panel>

                    @if($d->error)
                        <flux:tab.panel name="error">
                            <pre class="bg-red-50 dark:bg-red-950/20 text-red-900 dark:text-red-200 rounded p-3 text-xs overflow-x-auto">{{ $d->error }}</pre>
                        </flux:tab.panel>
                    @endif
                </flux:tab.group>

                <div class="flex gap-2">
                    <flux:spacer/>
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Close') }}</flux:button>
                    </flux:modal.close>
                    @if(auth()->user()->can('update', $webhook))
                        <flux:button variant="primary" icon="arrow-path" wire:click="redeliver('{{ $d->id }}')">
                            {{ __('Redeliver') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Rotated secret modal --}}
    <flux:modal name="rotated-secret" class="min-w-[28rem] max-w-2xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('New signing secret') }}</flux:heading>
            <flux:text>
                {{ __('The previous secret is invalidated. Update your receiver with this new value — we will not show it again.') }}
            </flux:text>
            <flux:input readonly copyable value="{{ $rotatedSecret }}"/>
            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="primary">{{ __("I've copied it") }}</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</x-layouts.repository>
