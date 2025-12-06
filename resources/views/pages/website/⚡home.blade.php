<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="server">Hosted in Frankfurt, Germany</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                The secure, compliant home for your containers
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Stop worrying about data sovereignty. We offer a high-performance Docker registry
                fully hosted in Germany with strict GDPR compliance and adaptive pricing that scales
                down when you do.
            </flux:subheading>

            <div class="mt-10 lg:mt-12 flex items-center gap-6">
                <flux:button href="{{ route('register') }}" variant="primary">
                    Start deploying
                </flux:button>

                <flux:button :href="route('website.pricing')" variant="outline" icon-trailing="arrow-right">
                    Check pricing
                </flux:button>
            </div>
        </div>
    </x-container>

    <x-container class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">
        <div>
            <flux:heading level="2"
                          class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                Zero friction migration
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                You don't need to learn a new tool. {{ config('app.name') }} works with the standard Docker CLI and all
                OCI-compliant
                clients. Just login, tag, and push.
            </flux:subheading>

            <div class="mt-8 flex flex-col gap-3">
                <span class="flex items-center gap-2">
                    <flux:icon name="check-circle" variant="mini"/>
                    <flux:text size="lg" class="ps-0">Compatible with Docker & Podman</flux:text>
                </span>


                <span class="flex items-center gap-2">
                    <flux:icon name="check-circle" variant="mini"/>
                    <flux:text size="lg" icon="check-circle"
                               class="ps-0">Works with GitHub Actions & GitLab CI</flux:text>
                </span>

                <span class="flex items-center gap-2">
                    <flux:icon name="check-circle" variant="mini"/>
                    <flux:text size="lg" icon="check-circle"
                               class="ps-0">High-speed uploads via 10Gbps uplink</flux:text>
                </span>
            </div>
        </div>

        
    </x-container>

    <x-container>
        <flux:heading level="2"
                      class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
            Why companies are switching
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10 lg:mt-12">
            <flux:card>
                <flux:icon name="shield-check" variant="solid" class="mb-4"/>
                <flux:heading level="3" class="mb-2">GDPR Compliant</flux:heading>
                <flux:text>
                    US Cloud Act concerns? We are a German company hosting exclusively on German bare-metal servers.
                    No data processing outside the EU.
                </flux:text>
            </flux:card>

            <flux:card>
                <flux:icon name="bolt" variant="solid" class="mb-4"/>
                <flux:heading level="3" class="mb-2">Bare Metal Speed</flux:heading>
                <flux:text>
                    We run on NVMe storage with unmetered 10Gbps uplinks. Experience pull speeds that make your CI/CD
                    pipelines fly.
                </flux:text>
            </flux:card>

            <flux:card>
                <flux:icon name="code-bracket-square" variant="solid" class="mb-4"/>
                <flux:heading level="3" class="mb-2">Built on Open Source</flux:heading>
                <flux:text>
                    Our core is open source. We contribute back to the ecosystem with tools like
                    <code>laravel-dockhand</code>.
                    No black boxes.
                </flux:text>
            </flux:card>
        </div>
    </x-container>
</div>
