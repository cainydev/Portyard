<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Appearance')] class extends Component {
    //
};
?>

<x-layouts.user-settings>
    <x-container inset>
        <x-app.settings.section
            :title="__('Appearance Settings')"
            :subtitle="__('Customize how the application looks for you.')">

            <div class="space-y-4 max-w-md">
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                    <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
                </flux:radio.group>

                <flux:text variant="subtle">
                    {{ __('Choose between light and dark mode, or let the application follow your system preference.') }}
                </flux:text>
            </div>
        </x-app.settings.section>
    </x-container>
</x-layouts.user-settings>
