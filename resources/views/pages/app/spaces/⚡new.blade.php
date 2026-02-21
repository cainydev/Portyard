<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex flex-col grow" x-data>
    <x-container class="flex flex-col grow p-0">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ __('Spaces') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('New Space') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('New Space')"
                              :subtitle="__('Create a new space to organize your repositories and collaborators.')">
            <flux:button :href="route('app.space.dashboard')" icon="arrow-left" variant="subtle"
                         wire:navigate.hover>
                {{ __('Back to Dashboard') }}
            </flux:button>
        </x-app.section-header>
    </x-container>
</div>
