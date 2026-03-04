<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="gift">Open Beta</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                Free while we're in beta
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Portyard is free to use during our open beta. Get 5 GB of storage per space,
                unlimited repositories, and unlimited team members — no credit card required.
            </flux:subheading>

            <div class="mt-10 lg:mt-12">
                @auth
                    <flux:button href="{{ route('root') }}" variant="primary">
                        View dashboard
                    </flux:button>
                @else
                    <flux:button href="{{ route('register') }}" variant="primary">
                        Join the beta
                    </flux:button>
                @endauth
            </div>
        </div>
    </x-container>

    <x-container inset border-bottom="center">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-6 lg:p-8 flex flex-col justify-center border-b lg:border-b-0 lg:border-r border-stitched">
                <flux:heading level="2" size="xl">During beta: completely free</flux:heading>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-5xl font-bold tracking-tight text-gray-900 dark:text-white">€0</span>
                    <span class="text-sm font-semibold text-gray-500">/month</span>
                </div>

                <flux:text class="mt-4">
                    Everything included, no credit card required.
                </flux:text>
            </div>

            <div class="grid grid-cols-[auto_1fr] grid-rows-4 items-center h-full text-sm">
                <div class="h-full content-center px-4 bg-green-50 dark:bg-green-950/20 border-b border-r border-stitched text-center">
                    <flux:icon name="check" variant="mini" class="text-green-500"/>
                </div>
                <div class="h-full content-center px-6 lg:px-8 border-b border-stitched text-gray-600 dark:text-gray-400">5 GB storage per space</div>

                <div class="h-full content-center px-4 bg-green-50 dark:bg-green-950/20 border-b border-r border-stitched text-center">
                    <flux:icon name="check" variant="mini" class="text-green-500"/>
                </div>
                <div class="h-full content-center px-6 lg:px-8 border-b border-stitched text-gray-600 dark:text-gray-400">Unlimited repositories</div>

                <div class="h-full content-center px-4 bg-green-50 dark:bg-green-950/20 border-b border-r border-stitched text-center">
                    <flux:icon name="check" variant="mini" class="text-green-500"/>
                </div>
                <div class="h-full content-center px-6 lg:px-8 border-b border-stitched text-gray-600 dark:text-gray-400">Unlimited team members</div>

                <div class="h-full content-center px-4 bg-green-50 dark:bg-green-950/20 border-r border-stitched text-center">
                    <flux:icon name="check" variant="mini" class="text-green-500"/>
                </div>
                <div class="h-full content-center px-6 lg:px-8 text-gray-600 dark:text-gray-400">GDPR-compliant hosting</div>
            </div>
        </div>
    </x-container>

    <x-container inset x-data="{
            storage: 100,
            _display: 100,
            _animating: false,
            _snaps: [10, 100, 250, 500, 1000],
            _snapThreshold: 20,
            get displayStorage() { return Math.round(this._display) },
            get storageCost() { return (this.storage * 0.02).toFixed(2) },
            get displayCost() { return (this.displayStorage * 0.02).toFixed(2) },
            get total() { return this.storageCost },
            get displayTotal() { return this.displayCost },
            _snap(val) {
                for (const s of this._snaps) {
                    if (Math.abs(val - s) <= this._snapThreshold) return s;
                }
                return val;
            },
            _animate() {
                if (this._animating) return;
                this._animating = true;
                const step = () => {
                    const diff = this.storage - this._display;
                    if (Math.abs(diff) < 0.5) {
                        this._display = this.storage;
                        this._animating = false;
                        return;
                    }
                    this._display += diff * 0.08;
                    requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            },
            init() {
                this.$watch('storage', (val) => {
                    const snapped = this._snap(val);
                    if (snapped !== val) this.storage = snapped;
                    this._animate();
                });
            }
        }">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:grid-rows-[auto_auto_auto]">
            {{-- Left column: subgrid for row alignment --}}
            <div class="border-b lg:border-b-0 lg:border-r border-stitched lg:grid lg:grid-rows-subgrid lg:row-span-3">
                <div class="p-6 lg:p-8">
                    <flux:heading level="2" size="xl">After beta: usage-based pricing</flux:heading>
                    <flux:subheading class="mt-2">
                        When the beta ends, we plan to introduce simple, linear pricing based on storage usage.
                        Pricing is subject to change.
                    </flux:subheading>

                    <div class="mt-8 flex justify-between items-baseline">
                        <flux:heading level="3" size="sm">Storage Amount</flux:heading>
                        <span class="text-sm tabular-nums text-gray-500" x-text="storage + ' GB'"></span>
                    </div>
                </div>

                <div class="px-6 lg:px-8 flex items-center">
                    <flux:slider x-model="storage" min="1" max="1000" step="1" class="w-full">
                        <flux:slider.tick value="10">10 GB</flux:slider.tick>
                        <flux:slider.tick value="100">100 GB</flux:slider.tick>
                        <flux:slider.tick value="250">250 GB</flux:slider.tick>
                        <flux:slider.tick value="500">500 GB</flux:slider.tick>
                        <flux:slider.tick value="1000">1 TB</flux:slider.tick>
                    </flux:slider>
                </div>

                <div class="px-6 lg:px-8 pb-6 lg:pb-8 pt-4">
                    <flux:text class="text-xs text-gray-500">Charged at €0.02 per GB. Prices exclude VAT where applicable. Subject to change.</flux:text>
                </div>
            </div>

            {{-- Right column: subgrid for row alignment --}}
            <div class="lg:grid lg:grid-rows-subgrid lg:row-span-3">
                <div class="p-6 lg:p-8">
                    <flux:heading level="2" size="lg">Estimated Monthly Cost</flux:heading>

                    <div class="mt-8 flex items-baseline gap-2">
                        <span class="text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
                            €<span x-text="displayTotal"></span>
                        </span>
                        <span class="text-sm font-semibold text-gray-500">/month</span>
                    </div>
                </div>

                <div class="px-6 lg:px-8 flex items-center">
                    <flux:progress ::value="_display" max="1000" color="zinc" class="h-1.5 w-full"/>
                </div>

                <div class="px-6 lg:px-8 pb-6 lg:pb-8 pt-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Storage (<span x-text="displayStorage"></span> GB)</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            €<span x-text="displayCost"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-container>

    <x-container inset>
        <div class="grid grid-cols-1 md:grid-cols-3 border-b border-stitched -mb-px">
            {{-- Heading row --}}
            <div class="p-6 lg:p-8 md:col-span-3">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Everything included
                </flux:heading>
            </div>

            {{-- Card row --}}
            <div class="p-6 lg:p-8 border-t border-stitched border-b md:border-b-0 md:border-r">
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="user-group" variant="mini"/>
                    <flux:heading level="3">No Per-Seat Pricing</flux:heading>
                </div>
                <flux:text>
                    Invite your whole team at no extra cost. During beta and after, there are no charges
                    per user — just add your collaborators and go.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched border-b md:border-b-0 md:border-r">
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="chart-bar" variant="mini"/>
                    <flux:heading level="3">Pay-as-You-Go</flux:heading>
                </div>
                <flux:text>
                    After beta, usage is billed to the exact MB. No tiers, no minimums — delete an image
                    and stop paying for it immediately.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched">
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="lifebuoy" variant="mini"/>
                    <flux:heading level="3">Fair Support</flux:heading>
                </div>
                <flux:text>
                    Every user gets email support and full docs access. We prioritize critical issues
                    regardless of your monthly spend.
                </flux:text>
            </div>
        </div>
    </x-container>
</div>
