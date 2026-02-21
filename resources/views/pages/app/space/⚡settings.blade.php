<?php

use App\Models\Space;
use Livewire\Component;

new class extends Component {
    public Space $space;

    public string $name;
    public ?string $description = null;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount(Space $space): void
    {
        $this->space = $space;
        $this->name = $space->name;
        $this->description = $space->description;
    }

    public function save(): void
    {
        $this->space->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        \Flux\Flux::toast(__('Space settings updated successfully.'), duration: 2000, variant: 'success');
    }

    public function render()
    {
        return $this->view()
            ->title(__('Space Settings'));
    }
};
?>


<div class="flex flex-col grow" x-data>
    <x-container class="flex flex-col grow p-0">
        {{-- Header --}}
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ $space->name }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Settings') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('Space Settings')"
                              :subtitle="__('Manage space members, repository defaults, billing and more.')">
        </x-app.section-header>

        <x-app.tabs>
            {{-- The Tab Navigation Buttons --}}
            <x-slot:tabs>
                <flux:tab name="general" icon="building-office">General</flux:tab>
                <flux:tab name="access" icon="users">Members & Robots</flux:tab>
                <flux:tab name="repositories" icon="cube">Repositories</flux:tab>
                <flux:tab name="lifecycle" icon="trash">Lifecycle</flux:tab>
                <flux:tab name="billing" icon="banknotes">Billing</flux:tab>
            </x-slot:tabs>

            {{-- 1. General --}}
            <flux:tab.panel name="general">
                <x-app.settings.section :title="__('General Settings')"
                                        :subtitle="__('Update the basic information and settings for this space.')">

                    <flux:input :label="__('Space Namespace')"
                                :description="__('The unique namespace used in repository paths.')"
                                :value="$space->namespace" class="max-w-md" disabled/>

                    <flux:input :label="__('Space Name')"
                                :description="__('The name of the space as it appears in the UI.')"
                                wire:model="name" class="max-w-md"/>

                    <flux:textarea :label="__('Space Description')"
                                   :description="__('A brief description of the space.')"
                                   wire:model="description" class="max-w-md" rows="3"/>

                    <flux:button class="mt-4 max-w-md" wire:click.prevent="save">Save Changes</flux:button>
                </x-app.settings.section>
            </flux:tab.panel>

            {{-- 2. Access (Humans + Robots) --}}
            <flux:tab.panel name="access">
                <x-app.settings.section :title="__('Coming Soon')"
                                        :subtitle="__('Access rules will be available in a future release. These will allow you to manage members and robot accounts with specific permissions for this space.')">
                </x-app.settings.section>
            </flux:tab.panel>

            {{--
            <flux:tab.panel name="access">
                <x-app.settings.section :title="__('Robot Accounts')"
                                        :subtitle="__('Robot accounts are automated users that can be used for CI/CD pipelines and other automated tasks. They can be granted specific permissions to interact with repositories in this space.')">
                    <x-slot:actions>
                        <flux:button size="sm" icon="plus">Create Robot</flux:button>
                    </x-slot:actions>
                </x-app.settings.section>

                <x-app.settings.section class="p-0!">
                    <flux:table class="border-stitched">
                        <flux:table.columns>
                            <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">Name
                            </flux:table.column>
                            <flux:table.column class="border-stitched">Grants</flux:table.column>
                            <flux:table.column class="border-stitched">Activity</flux:table.column>
                            <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px">Actions
                            </flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            <flux:table.row
                                class="hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20">
                                <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                    <flux:text variant="subtle">ci-builder-token</flux:text>
                                </flux:table.cell>
                                <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                    <flux:badge class="uppercase" size="sm">read</flux:badge>
                                    <flux:badge class="uppercase" size="sm">write</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                        <span class="flex flex-col gap-1">
                                            <flux:text>Last used 2 days ago</flux:text>
                                            <flux:text variant="subtle">Created 5 days ago</flux:text>
                                        </span>
                                </flux:table.cell>
                                <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                    <flux:button size="xs" variant="outline">Revoke</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </x-app.settings.section>
            </flux:tab.panel>--}}

            {{-- 3. Repositories --}}
            <flux:tab.panel name="repositories">
                <x-app.settings.section :title="__('Coming Soon')"
                                        :subtitle="__('Repository settings will be available in a future release. These will allow you to configure default behaviors and policies for repositories within this space.')">
                </x-app.settings.section>
            </flux:tab.panel>

            {{--
            <flux:tab.panel name="repositories">
                <x-app.settings.section :title="__('Creation Policy')">
                    <div x-data="{ pushToCreate: false }">
                        <flux:checkbox
                            x-model="pushToCreate"
                            label="Enable Push-to-Create"
                            description="Allow creating new repositories simply by pushing an image to a new path."
                            :checked="$space->enable_push_to_create"
                        />

                        <div class="-mt-4 ml-2 pt-8 pl-6 border-stitched border-l pb-2">
                            <flux:radio.group label="Default Visibility" :value="$space->default_visibility"
                                              class="mt-2">
                                <flux:radio value="private" checked label="Private" x-bind:disabled="!pushToCreate"
                                            description="New repositories are private by default."/>
                                <flux:radio value="public" label="Public" x-bind:disabled="!pushToCreate"
                                            description="New repositories are visible to anyone."/>
                            </flux:radio.group>
                        </div>
                    </div>
                </x-app.settings.section>

                <x-app.settings.section :title="__('Immutability')">
                    <flux:checkbox
                        label="Protect Tags by Default"
                        description="Prevent image tags (e.g. v1.0.0) from being overwritten once pushed. Users must use unique tags."
                        :checked="$space->default_tag_immutability"
                    />
                </x-app.settings.section>

                <x-app.settings.section :title="__('Security Scanning')">
                    <flux:checkbox
                        label="Auto-scan on push"
                        description="Automatically scan uploaded layers for CVEs (Common Vulnerabilities and Exposures)."
                        :checked="$space->auto_scan"
                    />
                </x-app.settings.section>
            </flux:tab.panel>--}}

            {{-- 4. Lifecycle --}}
            <flux:tab.panel name="lifecycle">
                <x-app.settings.section :title="__('Coming Soon')"
                                        :subtitle="__('Lifecycle management features will be available in a future release. These will allow you to define policies for automatic cleanup of old or unused images to save storage space.')">
                </x-app.settings.section>
            </flux:tab.panel>

            {{-- 5. Billing --}}
            <flux:tab.panel name="billing">
                <x-app.settings.section :title="__('Coming Soon')"
                                        :subtitle="__('Billing and subscription management features will be available in a future release. Until then, all spaces have access to all features without restrictions.')">
                </x-app.settings.section>
            </flux:tab.panel>

        </x-app.tabs>
    </x-container>
</div>
