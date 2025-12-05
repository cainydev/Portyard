<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="light-bulb">Commitment to the Community</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                Open Source is the foundation of everything we build
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                We are passionate about transparency, developer freedom, and open standards. By building our
                container registry core and utility packages in the open, we invite
                collaboration and ensure our tools are predictable, secure, and community-driven.
            </flux:subheading>
        </div>

        <div class="mt-10 lg:mt-12 flex items-center gap-6">
            <flux:button href="https://github.com/cainydev/Portyard" variant="primary" icon-trailing="arrow-right">
                Explore {{ config('app.name') }} on Github
            </flux:button>

            <flux:button href="https://github.com/cainydev" variant="outline" icon-trailing="users">
                Find Ways to Contribute
            </flux:button>
        </div>
    </x-container>

    <x-container>
        <flux:badge icon="star">Featured Contribution: Laravel Dockhand</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="2"
                          class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                Deep, OCI-Compliant Registry Integration for Laravel
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Laravel Dockhand is a highly technical integration package designed for production use, enabling
                Laravel applications to directly communicate with and automate tasks on any OCI-compliant registry.
            </flux:subheading>
        </div>

        <div class="mt-10 lg:mt-12 grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-3">
            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="server-stack" variant="mini"/>
                        <flux:heading level="3">OCI Distribution Ready</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Guiding principles derived from the **CNCF Distribution Documentation** ensure the package
                        is always current and compliant with industry standards for reliability and
                        interoperability.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="https://github.com/cainydev/laravel-dockhand#interacting-with-the-registry"
                                 variant="outline" size="sm" icon-trailing="arrow-right">
                        View API Methods
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="key" variant="mini"/>
                        <flux:heading level="3">JWT Security Token</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Designed for secure, production environments, the package requires key pairs for **signing
                        JWT tokens**, ensuring authenticated and secure API interaction with the registry.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="https://github.com/cainydev/laravel-dockhand#installation" variant="outline"
                                 size="sm" icon-trailing="arrow-right">
                        Setup Keys
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="rocket-launch" variant="mini"/>
                        <flux:heading level="3">Automated Registry Events</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Harness registry notifications (Manifest Pushed/Pulled, Blob Deleted) directly inside
                        Laravel through native events, enabling powerful, automated CI/CD workflows.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="https://github.com/cainydev/laravel-dockhand#listening-to-events"
                                 variant="outline" size="sm" icon-trailing="arrow-right">
                        Configure Notifications
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </x-container>
</div>
