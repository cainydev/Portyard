@props(['repository', 'breadcrumbs' => null, 'title' => null, 'subtitle' => null, 'actions' => null])

<div class="flex flex-col grow">
    <x-container :border-bottom="false">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ $repository->space->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Repositories</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $repository->name }}</flux:breadcrumbs.item>

            @if($breadcrumbs)
                @foreach ($breadcrumbs as $breadcrumb)
                    <flux:breadcrumbs.item>{{ $breadcrumb }}</flux:breadcrumbs.item>
                @endforeach
            @endif
        </flux:breadcrumbs>

        <x-app.section-header :title="$title ?? $repository->path"
                              :subtitle="$subtitle"
                              :actions="$actions"
                              class="mt-4"/>
    </x-container>

    <x-container sticky inset border="center">
        <x-app.tabs :border="false">
            <flux:navbar.item
                :href="route('app.space.repositories.overview', ['repository' => $repository])"
                wire:navigate
                icon="home"
            >
                {{ __('Overview') }}
            </flux:navbar.item>

            <flux:navbar.item
                :href="route('app.space.repositories.tags', ['repository' => $repository])"
                wire:navigate
                icon="tag"
            >
                {{ __('Tags') }}
            </flux:navbar.item>

            {{--
            <flux:navbar.item
                :href="route('app.space.repositories.collaborators', ['repository' => $repository])"
                wire:navigate
                icon="users"
            >
                {{ __('Collaborators') }}
            </flux:navbar.item>--}}

            @if(auth()->user()->can('manageSettings', $repository))
                <flux:navbar.item
                    :href="route('app.space.repositories.webhooks', ['repository' => $repository])"
                    :current="request()->routeIs('app.space.repositories.webhooks*')"
                    wire:navigate
                    icon="signal"
                >
                    {{ __('Webhooks') }}
                </flux:navbar.item>

                <flux:navbar.item
                    :href="route('app.space.repositories.settings', ['repository' => $repository])"
                    wire:navigate
                    icon="cog-6-tooth"
                >
                    {{ __('Settings') }}
                </flux:navbar.item>
            @endif
        </x-app.tabs>
    </x-container>

    {{ $slot }}
</div>
