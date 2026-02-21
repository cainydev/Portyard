<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="identification">Sovereign Infrastructure</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                Built for privacy-first container management
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Store, secure, and deploy your container images with the peace of mind that comes from strict German
                data laws and high-performance infrastructure.
            </flux:subheading>
        </div>

        <div class="mt-10 lg:mt-12 grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-3">
            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="server" variant="mini"/>
                        <flux:heading level="3">Hosted in Frankfurt</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Your data never leaves Germany. We run on Green Energy bare-metal servers in Frankfurt,
                        ensuring low latency for your EU customers and full GDPR compliance.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="#" variant="outline" size="sm" icon-trailing="arrow-right">
                        Read our DPA
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="shield-check" variant="mini"/>
                        <flux:heading level="3">Automatic CVE Scanning</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Don't deploy vulnerabilities. Every image pushed to your registry is automatically scanned
                        against the latest CVE databases, with detailed reports generated instantly.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="#" variant="outline" size="sm" icon-trailing="arrow-right">
                        View security
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <div>
                    <div class="flex items-center gap-x-3">
                        <flux:icon name="banknotes" variant="mini"/>
                        <flux:heading level="3">Adaptive Pricing</flux:heading>
                    </div>

                    <flux:text class="mt-4">
                        Stop paying for limits you don't hit. Our adaptive pricing model scales bandwidth and
                        storage costs linearly with your usage. No tiers, no lock-ins.
                    </flux:text>
                </div>

                <div class="grow flex items-end">
                    <flux:button href="#" variant="outline" size="sm" icon-trailing="arrow-right">
                        Calculate savings
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </x-container>

    <x-container class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-start">
        <div>
            <flux:heading level="2"
                          class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                Fine-grained Access Control
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Managing access shouldn't be a headache. Portyard allows you to create Organization
                Tokens for your CI/CD pipelines and servers, separate from your personal user account.
            </flux:subheading>

            <div class="mt-8 space-y-4">
                <flux:text>
                    <strong class="text-neutral-900 dark:text-white">Read-Only Tokens:</strong> Perfect for production
                    servers that only need to pull images.
                </flux:text>
                <flux:text>
                    <strong class="text-neutral-900 dark:text-white">Scoped Permissions:</strong> Limit tokens to
                    specific
                    repositories or namespaces.
                </flux:text>
            </div>
        </div>

        <div>
            <flux:card>
                <div class="flex items-center justify-between mb-6">
                    <flux:heading level="3">Access Tokens</flux:heading>
                    <flux:button size="sm" icon="plus">Create Token</flux:button>
                </div>
                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between p-3 rounded-lg border border-neutral-200 dark:border-neutral-400">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-100 dark:bg-green-900/10 text-green-500 rounded-md">
                                <flux:icon name="command-line" variant="mini"/>
                            </div>
                            <div>
                                <div class="font-medium text-sm">GitHub Actions CI</div>
                                <div class="text-xs text-neutral-500">Read & Write • Exp: Never</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="size-2 mr-3 rounded-full bg-green-500"></div>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between p-3 rounded-lg border border-neutral-200 dark:border-neutral-400">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/10 text-blue-500 rounded-md">
                                <flux:icon name="server" variant="mini"/>
                            </div>
                            <div>
                                <div class="font-medium text-sm">Production Pull</div>
                                <div class="text-xs text-neutral-500">Read Only • Exp: 30 days</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="size-2 mr-3 rounded-full bg-green-500"></div>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>


    </x-container>

    <x-container class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-start">
        <div>
            <flux:heading level="2"
                          class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                Seamless Integration
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                Portyard integrates effortlessly into your existing workflow. Whether you use GitHub Actions, GitLab CI,
                or custom pipelines, we fit right in.
            </flux:subheading>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <flux:card class="text-center hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                <flux:icon name="code-bracket" class="mx-auto mb-4 text-neutral-400"/>
                <flux:heading level="3" size="base">GitHub Actions</flux:heading>
            </flux:card>
            <flux:card class="text-center hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                <flux:icon name="command-line" class="mx-auto mb-4 text-neutral-400"/>
                <flux:heading level="3" size="base">GitLab CI</flux:heading>
            </flux:card>
            <flux:card class="text-center hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                <flux:icon name="cube" class="mx-auto mb-4 text-neutral-400"/>
                <flux:heading level="3" size="base">Kubernetes</flux:heading>
            </flux:card>
            <flux:card class="text-center hover:bg-neutral-50 dark:hover:bg-neutral-800 transition">
                <flux:icon name="globe-alt" class="mx-auto mb-4 text-neutral-400"/>
                <flux:heading level="3" size="base">Coolify / Forge</flux:heading>
            </flux:card>
        </div>
    </x-container>
</div>
