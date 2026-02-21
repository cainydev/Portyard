<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="banknotes">Transparent Pricing</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                Pay only what you use
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                No tiers, no overage fees, and no "enterprise" gatekeeping. Our adaptive pricing scales
                linearly with your storage needs and security activity.
            </flux:subheading>
        </div>
    </x-container>

    <x-container x-data="{
            storage: 100,
            scans: 20,
            get storageCost() { return (this.storage * 0.02).toFixed(2) },
            get scanCost() { return (Math.max(0, this.scans) * 0.05).toFixed(2) },
            get total() { return (parseFloat(this.storageCost) + parseFloat(this.scanCost)).toFixed(2) }
        }" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <div class="lg:col-span-7 flex flex-col gap-10">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading level="3" size="lg">Storage & Bandwidth</flux:heading>
                    <flux:subheading>Select your estimated monthly storage usage in Gigabytes.</flux:subheading>
                </div>

                <flux:field>
                    <flux:label>
                        Storage Amount (GB)
                    </flux:label>

                    <div class="flex items-center gap-4">
                        <flux:slider x-model="storage" min="1" max="1000" step="1" class="flex-1"/>
                    </div>

                    <flux:description>Charged at €0.02 per GB.</flux:description>
                </flux:field>
            </flux:card>

            <flux:card class="space-y-6">
                <div>
                    <flux:heading level="3" size="lg">Vulnerability Scans</flux:heading>
                    <flux:subheading>This depends on how frequently you set up your images to be scanned.
                    </flux:subheading>
                </div>

                <flux:field>
                    <flux:label>
                        Monthly Scans
                    </flux:label>

                    <div class="flex items-center gap-4">
                        <flux:slider x-model="scans" min="0" max="200" step="1" class="flex-1"/>
                    </div>

                    <flux:description>
                        Charged at €0.05 per scan.
                    </flux:description>
                </flux:field>
            </flux:card>
        </div>

        <div class="lg:col-span-5 lg:sticky lg:top-8">
            <flux:card>
                <flux:heading level="2" size="lg">Estimated Monthly Cost</flux:heading>

                <div class="mt-8 flex items-baseline gap-2">
                         <span class="text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
                            €<span x-text="total"></span>
                        </span>
                    <span class="text-sm font-semibold text-gray-500">/month</span>
                </div>

                <flux:separator class="my-8"/>

                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex justify-between">
                        <span>Storage (<span x-text="storage"></span> GB)</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                                €<span x-text="storageCost"></span>
                            </span>
                    </li>
                    <li class="flex justify-between">
                        <span>Security Scans (<span x-text="scans"></span>)</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                                <template x-if="scans <= 10">
                                    <span class="text-green-600">Free</span>
                                </template>
                                <template x-if="scans > 10">
                                    <span>€<span x-text="scanCost"></span></span>
                                </template>
                            </span>
                    </li>
                </ul>

                <div class="mt-8">
                    <flux:button href="#" variant="primary" class="w-full">
                        Start saving
                    </flux:button>
                    <p class="mt-4 text-xs text-center text-gray-500">
                        Credit card required to sign up. Prices exclude VAT where applicable.
                    </p>
                </div>
            </flux:card>
        </div>
    </x-container>

    <x-container>
        <flux:heading level="2"
                      class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
            Everything included
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
            <flux:card>
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="chart-bar" variant="mini"/>
                    <flux:heading level="3">Linear Scale</flux:heading>
                </div>
                <flux:text>
                    Usage is billed to the exact Megabyte. If you delete an image halfway through the month, you
                    stop paying for it immediately.
                </flux:text>
            </flux:card>

            <flux:card>
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="user-group" variant="mini"/>
                    <flux:heading level="3">Unlimited Seats</flux:heading>
                </div>
                <flux:text>
                    We don't charge per user. Invite your whole team, create organization tokens, and manage
                    permissions without extra costs.
                </flux:text>
            </flux:card>

            <flux:card>
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon name="lifebuoy" variant="mini"/>
                    <flux:heading level="3">Fair Support</flux:heading>
                </div>
                <flux:text>
                    Every customer gets access to email support and our documentation. We prioritize critical issues
                    regardless of your monthly spend.
                </flux:text>
            </flux:card>
        </div>
    </x-container>
</div>
