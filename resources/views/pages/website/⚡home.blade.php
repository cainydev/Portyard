<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::website')] class extends Component {
    //
};
?>

<div>
    <div>
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
                <flux:button href="#" variant="primary">
                    Start deploying
                </flux:button>

                <flux:button :href="route('website.pricing')" variant="outline" icon-trailing="arrow-right">
                    Check pricing
                </flux:button>
            </div>
        </div>
    </div>

    <x-website.divider/>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">
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

        <div
            class="rounded-xl overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 bg-neutral-700/50 border-b border-neutral-800">
                <div class="size-3 rounded-full bg-red-500"></div>
                <div class="size-3 rounded-full bg-yellow-500"></div>
                <div class="size-3 rounded-full bg-green-500"></div>
            </div>
            <div class="p-6 text-gray-300 bg-neutral-900/50 font-mono">
                <div class="flex">
                    <span class="text-yellow-400 mr-2 font-semibold">λ ~</span>
                    <span>docker login portyard.de</span>
                </div>
                <div class="text-gray-500 mb-4">Login Succeeded</div>

                <div class="flex">
                    <span class="text-yellow-400 mr-2 font-semibold">λ ~</span>
                    <span>docker tag my-app portyard.de/team/app:v1</span>
                </div>

                <div class="flex mt-4">
                    <span class="text-yellow-400 mr-2 font-semibold">λ ~</span>
                    <span>docker push portyard.de/team/app:v1</span>
                </div>
                <div class="text-gray-500">
                    The push refers to repository [portyard.de/team/app]<br>
                    8e674ad98d9c: Pushed <span class="text-gray-600">[212MB @ 80MB/s]</span><br>
                    v1: digest: sha256:8b9... size: 1288
                </div>
            </div>
        </div>
    </div>

    <x-website.divider/>

    <div>
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
    </div>
</div>
