<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ $title ?? config('app.name') }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    @php($isAppRoute = Auth::check() && (Route::is('app.*') || Route::is('root')))

    {{-- HEADER --}}
    <flux:header sticky class="p-0! items-stretch">
        <x-container :section="false" class="flex flex-row items-center px-6 lg:px-8">

            {{-- Mobile Toggle --}}
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

            {{-- Logo --}}
            <div class="flex items-center h-full">
                <x-app-logo :href="route('root')" wire:navigate/>
                <x-beta-badge class="ml-2"/>
            </div>

            <x-y-divider/>

            {{-- Desktop Navigation --}}
            <flux:navbar class="-mb-px max-lg:hidden">
                @if($isAppRoute)
                    {{-- App Routes --}}
                    <flux:navbar.item :href="route('root')"
                                      wire:navigate.hover
                                      x-data
                                      x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                                      x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                        Home
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('app.repositories.list')" wire:navigate.hover>Repositories
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('app.settings.profile')" wire:navigate.hover>Settings
                    </flux:navbar.item>
                @else
                    {{-- Website Routes --}}
                    <flux:navbar.item :href="route('root')"
                                      wire:navigate.hover
                                      x-data
                                      x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                                      x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                        Start
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('website.features')" wire:navigate.hover>Features</flux:navbar.item>
                    <flux:navbar.item :href="route('website.oss')" wire:navigate.hover>Open Source</flux:navbar.item>
                    <flux:navbar.item :href="route('website.docs')" wire:navigate.hover>Docs</flux:navbar.item>
                    <flux:navbar.item :href="route('website.pricing')" wire:navigate.hover>Pricing</flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer/>

            {{-- Right Actions --}}
            @auth
                @if(!Route::is('app.*') && !Route::is('root'))
                    <flux:navbar class="me-4 max-lg:hidden">
                        <flux:navbar.item variant="outline" wire:navigate.hover :href="route('app.repositories.list')">
                            Dashboard
                        </flux:navbar.item>
                    </flux:navbar>
                @endif

                <flux:dropdown position="top" align="start">
                    <flux:profile avatar="https://unavatar.io/{{ auth()->user()->email }}"/>
                    <flux:menu>
                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('app.settings.profile')" icon="cog" wire:navigate.hover>
                                {{ __('Settings') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator/>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @endauth

            @guest
                <flux:navbar class="max-lg:hidden gap-2">
                    <flux:navbar.item variant="subtle" :href="route('login')" wire:navigate>Login</flux:navbar.item>
                    <flux:navbar.item variant="primary" :href="route('register')" wire:navigate>Sign up
                    </flux:navbar.item>
                </flux:navbar>
            @endguest
        </x-container>
    </flux:header>

    {{-- MOBILE SIDEBAR --}}
    <flux:sidebar sticky collapsible="mobile"
                  class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo/>
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"/>
        </flux:sidebar.header>

        <flux:sidebar.nav>
            @if($isAppRoute)
                <flux:sidebar.item :href="route('root')"
                                   icon="home"
                                   wire:navigate
                                   x-data
                                   x-on:livewire:navigated.window="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)"
                                   x-init="$el.toggleAttribute('data-current', window.location.pathname === new URL($el.href).pathname)">
                    Home
                </flux:sidebar.item>
                <flux:sidebar.item :href="route('app.repositories.list')" icon="archive-box" wire:navigate>
                    Repositories
                </flux:sidebar.item>
                <flux:sidebar.item :href="route('app.settings.profile')" icon="cog" wire:navigate>Settings
                </flux:sidebar.item>
            @else
                <flux:sidebar.item :href="route('root')" wire:navigate>
                    Start
                </flux:sidebar.item>
                <flux:sidebar.item :href="route('website.features')" wire:navigate>Features</flux:sidebar.item>
                <flux:sidebar.item :href="route('website.oss')" wire:navigate>Open Source</flux:sidebar.item>
                <flux:sidebar.item :href="route('website.docs')" wire:navigate>Docs</flux:sidebar.item>
                <flux:sidebar.item :href="route('website.pricing')" wire:navigate>Pricing</flux:sidebar.item>
            @endif
        </flux:sidebar.nav>

        <flux:sidebar.spacer/>

        @auth
            <flux:sidebar.nav>
                <flux:sidebar.item :href="route('app.repositories.list')" icon="squares-2x2" wire:navigate>Dashboard
                </flux:sidebar.item>
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <flux:sidebar.item type="submit" icon="arrow-right-start-on-rectangle">Logout</flux:sidebar.item>
                </form>
            </flux:sidebar.nav>
        @endauth

        @guest
            <flux:sidebar.nav>
                <flux:sidebar.item :href="route('login')" icon="arrow-right-end-on-rectangle" wire:navigate>Login
                </flux:sidebar.item>
                <flux:sidebar.item :href="route('register')" icon="user-plus" wire:navigate>Sign up</flux:sidebar.item>
            </flux:sidebar.nav>
        @endguest
    </flux:sidebar>

    {{-- MAIN CONTENT --}}
    <flux:main class="flex flex-col p-0!">
        {{ $slot }}
    </flux:main>

    {{-- FOOTER --}}
    <flux:footer class="p-0! items-stretch">
        <x-container :section="false" :divide="false" class="flex items-center justify-between px-6 lg:px-8 py-4">
            <div class="flex justify-center items-center gap-4">
                <a href="#" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <span class="sr-only">X (Twitter)</span>
                    <x-svg.twitter class="h-4"/>
                </a>
                <a href="https://github.com/cainydev/portyard"
                   class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <span class="sr-only">GitHub</span>
                    <x-svg.github class="h-[18px]"/>
                </a>
            </div>

            <flux:text size="sm">
                &copy; 2025 {{ config('app.name') ?? 'Portyard' }}. All rights reserved.
            </flux:text>
        </x-container>
    </flux:footer>

    @fluxScripts
</body>
</html>
