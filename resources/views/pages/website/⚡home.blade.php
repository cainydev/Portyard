<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout("layouts::app")] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="rocket-launch">Open Beta — Free to Use</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1" class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                The secure, compliant home for your containers
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Stop worrying about data sovereignty. We offer a high-performance Docker registry fully hosted in
                Germany with strict GDPR compliance. Free during our open beta with 5 GB of storage per space.
            </flux:subheading>

            <div class="mt-10 lg:mt-12 flex items-center gap-6">
                @auth
                    <flux:button :href="route('root')" variant="primary">View dashboard</flux:button>
                @else
                    <flux:button :href="route('register')" variant="primary">Join the beta</flux:button>
                @endauth

                <flux:button :href="route('website.features')" variant="outline" icon-trailing="arrow-right">
                    Explore features
                </flux:button>
            </div>
        </div>
    </x-container>

    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-stitched">
                <flux:heading level="2" class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Zero friction migration
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    You don't need to learn a new tool. {{ config("app.name") }} works with the standard Docker CLI and all
                    OCI-compliant clients. Just login, tag, and push.
                </flux:subheading>

                <div class="mt-8">
                    <flux:button :href="route('website.docs.overview')" variant="outline" icon-trailing="book-open">
                        Read the docs
                    </flux:button>
                </div>
            </div>

            {{-- Terminal mock --}}
            <div class="flex flex-col font-mono text-sm">
                {{-- Terminal header --}}
                <div class="flex items-center border-b border-stitched">
                    <div class="flex items-center gap-2 px-6 lg:px-8 py-3">
                        <div class="size-3 rounded-full bg-red-400"></div>
                        <div class="size-3 rounded-full bg-yellow-400"></div>
                        <div class="size-3 rounded-full bg-green-400"></div>
                    </div>
                    <div class="grow bg-diag-lines self-stretch border-l border-stitched"></div>
                </div>

                {{-- Terminal content --}}
                <div class="px-6 lg:px-8 py-4 lg:py-5 space-y-3 text-zinc-600 dark:text-zinc-400">
                    <div>
                        <span class="text-zinc-400">$</span>
                        <span class="text-zinc-900 dark:text-zinc-100"> docker login portyard.de</span>
                    </div>
                    <div class="pl-4 text-green-600 dark:text-green-400">Login Succeeded</div>

                    <div>
                        <span class="text-zinc-400">$</span>
                        <span class="text-zinc-900 dark:text-zinc-100"> docker tag busybox:latest portyard.de/john/busybox:latest</span>
                    </div>

                    <div>
                        <span class="text-zinc-400">$</span>
                        <span class="text-zinc-900 dark:text-zinc-100"> docker push portyard.de/john/busybox:latest</span>
                    </div>
                    <div class="pl-4">latest: digest: sha256:5b3e6d…f4a3c2 size: 528</div>
                    <div class="pl-4 text-green-600 dark:text-green-400">Pushed successfully ✓</div>
                </div>
            </div>
        </div>
    </x-container>

    <x-container inset>
        <div class="grid grid-cols-1 md:grid-cols-3 border-b border-stitched -mb-px">
            {{-- Heading row --}}
            <div class="p-6 lg:p-8 md:col-span-3">
                <flux:heading
                    level="2"
                    class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Why developers are switching
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Built from the ground up for teams who care about where their data lives.
                    Every feature is designed around privacy, performance, and a developer experience
                    that gets out of your way.
                </flux:subheading>
            </div>

            {{-- Card row --}}
            <div class="p-6 lg:p-8 border-t border-stitched border-b md:border-b-0 md:border-r">
                <flux:icon name="shield-check" variant="solid" class="mb-4" />
                <flux:heading level="3" class="mb-2">GDPR Compliant</flux:heading>
                <flux:text>
                    US Cloud Act concerns? We are a German company hosting exclusively on German bare-metal servers. No
                    data processing outside the EU.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched border-b md:border-b-0 md:border-r">
                <flux:icon name="bolt" variant="solid" class="mb-4" />
                <flux:heading level="3" class="mb-2">Bare Metal Speed</flux:heading>
                <flux:text>
                    We run on NVMe storage with unmetered 10Gbps uplinks. Experience pull speeds that make your CI/CD
                    pipelines fly.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched">
                <flux:icon name="code-bracket-square" variant="solid" class="mb-4" />
                <flux:heading level="3" class="mb-2">Built on Open Source</flux:heading>
                <flux:text>
                    Our core is open source. We contribute back to the ecosystem with tools like
                    <code>laravel-dockhand</code>
                    . No black boxes.
                </flux:text>
            </div>
        </div>
    </x-container>
</div>
