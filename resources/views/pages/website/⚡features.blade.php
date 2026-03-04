<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-3">
            {{-- Hero row --}}
            <div class="p-6 lg:p-8 lg:col-span-2">
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
            </div>
            <div class="hidden lg:block border-stitched bg-diag-lines lg:border-l"></div>

            {{-- Feature cards row --}}
            <div class="p-6 lg:p-8 border-t border-stitched border-b lg:border-b-0 lg:border-r">
                <div class="flex items-center gap-x-3">
                    <flux:icon name="server" variant="mini"/>
                    <flux:heading level="3">Hosted in Frankfurt</flux:heading>
                </div>

                <flux:text class="mt-4">
                    Your data never leaves Germany. We run on Green Energy bare-metal servers in Frankfurt,
                    ensuring low latency for your EU customers and full GDPR compliance.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched border-b lg:border-b-0 lg:border-r">
                <div class="flex items-center gap-x-3">
                    <flux:icon name="clipboard-document-list" variant="mini"/>
                    <flux:heading level="3">Activity Logging</flux:heading>
                </div>

                <flux:text class="mt-4">
                    Every push, pull, and team action is recorded. Get full visibility into what's happening
                    across your spaces and repositories with detailed activity logs.
                </flux:text>
            </div>

            <div class="p-6 lg:p-8 border-t border-stitched">
                <div class="flex items-center gap-x-3">
                    <flux:icon name="gift" variant="mini"/>
                    <flux:heading level="3">Free During Beta</flux:heading>
                </div>

                <flux:text class="mt-4">
                    Get started with 5 GB of storage per space at no cost and no credit card required.
                    Usage-based pricing will be introduced after the beta period ends.
                </flux:text>
            </div>
        </div>
    </x-container>

    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-stitched">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Team & Access Management
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Collaborate with your team without compromise. Portyard gives you flexible controls
                    to manage who can access your spaces and what they can do.
                </flux:subheading>

                <div class="mt-8 flex flex-col gap-3">
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Invite team members to spaces</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Assign Owner, Admin, or Member roles</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Full activity logging across all actions</flux:text>
                    </span>
                    <span class="flex items-center gap-2">
                        <flux:icon name="check-circle" variant="mini"/>
                        <flux:text size="lg">Access tokens coming soon</flux:text>
                    </span>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="px-6 lg:px-8 pt-6 lg:pt-8 flex items-center justify-between pb-6">
                    <flux:heading level="3">Team Members</flux:heading>
                    <flux:button size="sm" icon="plus">Invite</flux:button>
                </div>

                <div class="border-b border-stitched"></div>

                <div class="px-6 lg:px-8 py-3 border-b border-stitched flex items-center gap-3">
                    <div class="size-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-medium">AC</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm">Alex Chen</div>
                    </div>
                    <flux:badge size="sm" color="indigo">Owner</flux:badge>
                </div>

                <div class="px-6 lg:px-8 py-3 border-b border-stitched flex items-center gap-3">
                    <div class="size-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-medium">JL</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm">Jordan Lee</div>
                    </div>
                    <flux:badge size="sm" color="amber">Admin</flux:badge>
                </div>

                <div class="px-6 lg:px-8 py-3 border-b border-stitched flex items-center gap-3">
                    <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-400 text-xs font-medium">SR</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm">Sam Rivera</div>
                    </div>
                    <flux:badge size="sm">Member</flux:badge>
                </div>

                <div class="grow bg-diag-lines"></div>
            </div>
        </div>
    </x-container>

    <x-container inset>
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_1fr] lg:grid-cols-[2fr_1fr_1fr] border-b border-stitched -mb-px">
            {{-- Heading: spans left column, 2 rows on lg --}}
            <div class="p-6 lg:p-8 sm:col-span-3 lg:col-span-1 lg:row-span-2 border-b lg:border-b-0 lg:border-r border-stitched">
                <flux:heading level="2"
                              class="text-4xl! font-semibold tracking-tight text-balance sm:text-5xl! max-w-4xl">
                    Seamless Integration
                </flux:heading>

                <flux:subheading class="mt-8 text-xl max-w-2xl">
                    Portyard integrates effortlessly into your existing workflow. Whether you use GitHub Actions, GitLab CI,
                    or custom pipelines, we fit right in.
                </flux:subheading>
            </div>

            {{-- 4 integration items: 2×2 grid on right --}}
            <div class="p-6 lg:p-8 text-center border-b border-stitched border-r">
                <flux:icon name="code-bracket" class="mx-auto mb-4 text-zinc-400"/>
                <flux:heading level="3" size="base">GitHub Actions</flux:heading>
            </div>
            <div class="p-6 lg:p-8 text-center border-b border-stitched">
                <flux:icon name="command-line" class="mx-auto mb-4 text-zinc-400"/>
                <flux:heading level="3" size="base">GitLab CI</flux:heading>
            </div>
            <div class="p-6 lg:p-8 text-center border-r border-stitched sm:border-b-0">
                <flux:icon name="cube" class="mx-auto mb-4 text-zinc-400"/>
                <flux:heading level="3" size="base">Kubernetes</flux:heading>
            </div>
            <div class="p-6 lg:p-8 text-center">
                <flux:icon name="globe-alt" class="mx-auto mb-4 text-zinc-400"/>
                <flux:heading level="3" size="base">Coolify / Forge</flux:heading>
            </div>
        </div>
    </x-container>
</div>
