<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ __('Decline invitation') }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main class="grid min-h-screen p-0! w-full place-items-center">
        <div class="flex flex-col gap-6 max-w-md w-full p-6 lg:p-8">
            <div>
                <flux:heading size="xl">
                    {{ __('Decline invitation') }}
                </flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    {{ __('Decline the invitation to :space?', ['space' => $invitation->space->name]) }}
                </flux:text>
            </div>

            <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}" class="flex flex-col gap-3">
                @csrf
                <flux:button type="submit" variant="danger">
                    {{ __('Decline invitation') }}
                </flux:button>
            </form>

            <flux:button :href="route('root')" variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </flux:main>

    @fluxScripts
</body>
</html>
