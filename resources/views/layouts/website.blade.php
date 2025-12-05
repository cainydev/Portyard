<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container sticky
                 class="bg-white dark:bg-zinc-800 border-b border-dashed border-zinc-300 dark:border-zinc-600 [&>div]:border-x [&>div]:border-dashed [&>div]:border-zinc-300 dark:[&>div]:border-zinc-600">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" x-persist="mobile-toggle"/>

        <div class="flex items-center h-full" x-persist="app-logo">
            <x-app-logo :href="route('website.home')"/>
            <x-beta-badge/>
        </div>

        <x-y-divider/>

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item :href="route('website.home')"
                              wire:navigate.hover
                              x-data
                              x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                              x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                Start
            </flux:navbar.item>
            <flux:navbar.item :href="route('website.features')"
                              wire:navigate.hover>Features
            </flux:navbar.item>
            <flux:navbar.item :href="route('website.oss')" wire:navigate.hover>Open
                Source
            </flux:navbar.item>
            <flux:navbar.item :href="route('website.docs')" wire:navigate.hover>
                Documentation
            </flux:navbar.item>
            <flux:navbar.item :href="route('website.pricing')"
                              wire:navigate.hover>Pricing
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer/>

        @auth
            <flux:navbar class="me-4">
                <flux:navbar.item variant="outline" wire:navigate.hover :href="route('app.dashboard')"
                                  x-data
                                  x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                                  x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                    Dashboard
                </flux:navbar.item>
            </flux:navbar>

            <flux:dropdown position="top" align="start" x-persist="navbar-spacer">
                <flux:profile avatar="https://unavatar.io/{{ auth()->user()->email }}"/>
                <flux:menu>
                    <form action="#">
                        <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth

        @guest
            <flux:navbar class="me-4">
                <flux:navbar.item variant="outline" href="#">Login</flux:navbar.item>
                <flux:navbar.item variant="primary" href="#">Sign up</flux:navbar.item>
            </flux:navbar>
        @endguest
    </flux:header>
    <flux:sidebar sticky collapsible="mobile"
                  class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo/>

            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"/>
        </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.item :href="route('website.home')"
                               wire:navigate.hover
                               x-data
                               x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                               x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                Start
            </flux:sidebar.item>
            <flux:sidebar.item :href="route('website.features')"
                               wire:navigate.hover>Features
            </flux:sidebar.item>
            <flux:sidebar.item :href="route('website.oss')" wire:navigate.hover>Open
                Source
            </flux:sidebar.item>
            <flux:sidebar.item :href="route('website.docs')" wire:navigate.hover>
                Documentation
            </flux:sidebar.item>
            <flux:sidebar.item :href="route('website.pricing')"
                               wire:navigate.hover>Pricing
            </flux:sidebar.item>
        </flux:sidebar.nav>
        <flux:sidebar.spacer/>
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:main container
               class="border-x border-dashed border-zinc-300 dark:border-zinc-600">
        {{ $slot }}
    </flux:main>

    <flux:footer container x-persist="website.footer"
                 class="bg-white dark:bg-zinc-800 border-t border-dashed border-zinc-300 dark:border-zinc-600 flex flex-row [&>div]:py-4 [&>div]:px-6 lg:[&>div]:px-8 [&>div]:border-x [&>div]:border-dashed [&>div]:border-zinc-300 dark:[&>div]:border-zinc-600">
        <div class="flex items-center justify-between">
            <div class="flex justify-center items-center gap-4">
                <a href="#" class="text-white">
                    <span class="sr-only">X (Twitter)</span>
                    <x-svg.twitter class="h-4"/>
                </a>

                <a href="https://github.com/cainydev/portyard" class="text-white">
                    <span class="sr-only">GitHub</span>
                    <x-svg.github class="h-[18px]"/>
                </a>
            </div>

            <flux:text size="sm">
                &copy; 2025 {{ config('app.name') ?? 'Your Container Registry' }}. All rights reserved.
            </flux:text>
        </div>
    </flux:footer>

    @fluxScripts
</body>
</html>
