<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    {{-- Section 1: Hero --}}
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr]">
            <div class="p-6 lg:p-8 lg:border-r border-stitched">
                <flux:badge icon="light-bulb">Commitment to the Community</flux:badge>

                <div class="mt-10 lg:mt-12">
                    <flux:heading level="1"
                                  class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                        Open Source is the foundation of everything we build
                    </flux:heading>

                    <flux:subheading class="mt-8 text-xl max-w-2xl">
                        We are passionate about transparency, developer freedom, and open standards. By building our
                        container registry and utility packages in the open, we invite collaboration and ensure our
                        tools are predictable, secure, and community-driven.
                    </flux:subheading>
                </div>

                <div class="mt-10 lg:mt-12 flex items-center gap-6">
                    <flux:button href="https://github.com/cainydev/Portyard" variant="primary" icon-trailing="arrow-right">
                        Explore on GitHub
                    </flux:button>

                    <flux:button href="https://github.com/cainydev" variant="outline" icon-trailing="users">
                        Find Ways to Contribute
                    </flux:button>
                </div>
            </div>

            <div class="hidden lg:flex flex-col">
                <div class="grow bg-diag-lines border-b border-stitched"></div>
                <div class="p-6 lg:p-8 border-b border-stitched flex items-center gap-3">
                    <flux:icon name="code-bracket-square" variant="mini" class="text-zinc-400"/>
                    <flux:text size="lg" class="font-medium">Open Source</flux:text>
                </div>
                <div class="p-6 lg:p-8 border-b border-stitched flex items-center gap-3">
                    <flux:icon name="users" variant="mini" class="text-zinc-400"/>
                    <flux:text size="lg" class="font-medium">Community Driven</flux:text>
                </div>
                <div class="p-6 lg:p-8 border-b border-stitched flex items-center gap-3">
                    <flux:icon name="server" variant="mini" class="text-zinc-400"/>
                    <flux:text size="lg" class="font-medium">Self-Hostable</flux:text>
                </div>
                <div class="p-6 lg:p-8 flex items-center gap-3">
                    <flux:icon name="cube" variant="mini" class="text-zinc-400"/>
                    <flux:text size="lg" class="font-medium">OCI Compliant</flux:text>
                </div>
            </div>
        </div>
    </x-container>

    {{-- Section 2: Repositories --}}
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-2 border-b border-stitched -mb-px">
            {{-- Heading row --}}
            <div class="p-6 lg:p-8 lg:col-span-2">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Our Open Source Projects
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Every line of code we write is available for inspection, contribution, and reuse.
                </flux:subheading>
            </div>

            {{-- Card: Portyard --}}
            <div class="p-6 lg:p-8 border-t border-stitched border-b lg:border-b-0 lg:border-r flex flex-col">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="cube" variant="mini"/>
                        <flux:heading level="3">{{ config('app.name') }}</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        The container registry platform itself. A privacy-first, GDPR-compliant Docker registry
                        built with Laravel, designed to give teams full control over their container images.
                    </flux:text>
                </div>

                <div class="grow flex items-end pt-6">
                    <flux:button href="https://github.com/cainydev/Portyard"
                                 variant="outline" size="sm" icon-trailing="arrow-right">
                        View Repository
                    </flux:button>
                </div>
            </div>

            {{-- Card: Laravel Dockhand --}}
            <div class="p-6 lg:p-8 border-t border-stitched flex flex-col">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="server-stack" variant="mini"/>
                        <flux:heading level="3">Laravel Dockhand</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        An OCI-compliant registry integration package for Laravel. Enables applications to
                        communicate with and automate tasks on any OCI-compliant container registry.
                    </flux:text>
                </div>

                <div class="grow flex items-end pt-6">
                    <flux:button href="https://github.com/cainydev/laravel-dockhand"
                                 variant="outline" size="sm" icon-trailing="arrow-right">
                        View Repository
                    </flux:button>
                </div>
            </div>
        </div>
    </x-container>

    {{-- Section 3: Why Open Source --}}
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-stitched">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Why we build in the open
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Trust is earned through transparency. We believe the best infrastructure software is built
                    where everyone can see, audit, and improve it.
                </flux:subheading>
            </div>

            <div class="p-6 lg:p-8 flex flex-col justify-center">
                <div class="flex flex-col gap-3">
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Full source code visibility</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Community-driven development</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">No vendor lock-in</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Security through transparency</flux:text>
                    </span>
                </div>
            </div>
        </div>
    </x-container>

    {{-- Section 4: Contribute CTA --}}
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] border-b border-stitched -mb-px">
            <div class="p-6 lg:p-8">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Want to contribute?
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Whether it's reporting bugs, improving docs, or submitting pull requests — every
                    contribution makes {{ config('app.name') }} better for everyone.
                </flux:subheading>

                <div class="mt-8">
                    <flux:button href="https://github.com/cainydev" variant="primary" icon-trailing="arrow-right">
                        Visit our GitHub
                    </flux:button>
                </div>
            </div>

            <div class="hidden lg:block border-l border-stitched bg-diag-lines"></div>
        </div>
    </x-container>
</div>
