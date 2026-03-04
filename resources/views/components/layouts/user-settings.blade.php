@props(['title' => null, 'subtitle' => null])

<div class="flex flex-col grow">
    <x-container :border-bottom="false">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Settings') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-app.section-header
            :title="$title ?? __('User Settings')"
            :subtitle="$subtitle ?? __('Manage your personal profile, security, and preferences.')"
            class="mt-4"
        />
    </x-container>

    <x-container sticky inset border="center">
        <x-app.tabs :border="false">
            <flux:navbar.item :href="route('app.user-settings.profile')" wire:navigate icon="user">
                {{ __('Profile') }}
            </flux:navbar.item>
            <flux:navbar.item :href="route('app.user-settings.security')" wire:navigate icon="shield-check">
                {{ __('Security') }}
            </flux:navbar.item>
            <flux:navbar.item :href="route('app.user-settings.appearance')" wire:navigate icon="swatch">
                {{ __('Appearance') }}
            </flux:navbar.item>
        </x-app.tabs>
    </x-container>

    {{ $slot }}
</div>
