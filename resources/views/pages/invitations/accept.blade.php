<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ __('Accept invitation') }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main class="grid min-h-screen p-0! w-full place-items-center">
        <div class="flex flex-col gap-6 max-w-md w-full p-6 lg:p-8">
            <div>
                <flux:heading size="xl">
                    {{ __('Join :space', ['space' => $invitation->space->name]) }}
                </flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    {{ __(':inviter invited you to join as :role.', [
                        'inviter' => $invitation->inviter?->name ?? __('Someone'),
                        'role' => ucfirst($invitation->role->value),
                    ]) }}
                </flux:text>
            </div>

            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="flex flex-col gap-3">
                @csrf
                <flux:button type="submit" variant="primary" icon="check">
                    {{ __('Accept invitation') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}" class="flex flex-col gap-3">
                @csrf
                <flux:button type="submit" variant="ghost">
                    {{ __('Decline instead') }}
                </flux:button>
            </form>
        </div>
    </flux:main>

    @fluxScripts
</body>
</html>
