@props([
    "activity",
    "showRepository" => true,
])

@php
    use App\Enums\Action;

    $user = $activity->user;
    $subject = $activity->subject;

    $getUserSpaceUrl = function ($userModel) {
        if (! $userModel) {
            return null;
        }

        $personalSpace = $userModel->personalSpace;

        return $personalSpace ? route("app.space.dashboard", $personalSpace) : null;
    };

    $actorUrl = $getUserSpaceUrl($user);
@endphp

<flux:text {{ $attributes }} size="lg">
    @if ($user && $actorUrl)
        <flux:link wire:navigate :href="$actorUrl" variant="default">{{ $user->name }}</flux:link>
    @elseif ($user)
        <flux:text inline variant="strong">{{ $user->name }}</flux:text>
    @else
        <flux:text inline variant="strong">System</flux:text>
    @endif

    @switch($activity->action)
        @case(Action::UserLoggedIn)
            logged in.

            @break
        @case(Action::UserRegistered)
            registered an account.

            @break
        @case(Action::UserUpdated)
            updated their profile.

            @break
        @case(Action::UserPasswordUpdated)
            changed their password.

            @break
        @case(Action::SpaceCreated)
            created the space

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.dashboard', $subject)">
                    {{ $subject->name }}
                </flux:link>
                .
            @else
                <flux:text inline variant="strong">a space</flux:text>
                .
            @endif

            @break
        @case(Action::SpaceUpdated)
            updated the space

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.dashboard', $subject)">
                    {{ $subject->name }}
                </flux:link>
                .
            @else
                <flux:text inline variant="strong">a space</flux:text>
                .
            @endif

            @break
        @case(Action::SpaceDeleted)
            deleted a space.

            @break
        @case(Action::MemberInvited)
            invited

            @if ($subject && $getUserSpaceUrl($subject))
                <flux:link wire:navigate :href="$getUserSpaceUrl($subject)">{{ $subject->name }}</flux:link>
            @elseif ($subject)
                <flux:text inline variant="strong">{{ $subject->name }}</flux:text>
            @else
                <flux:text inline variant="strong">a user</flux:text>
            @endif
            to the space.

            @break
        @case(Action::MemberAccepted)
            joined the space.

            @break
        @case(Action::MemberDeclined)
            declined the space invitation.

            @break
        @case(Action::MemberRoleUpdated)
            updated the role of

            @if ($subject && $getUserSpaceUrl($subject))
                <flux:link wire:navigate :href="$getUserSpaceUrl($subject)">{{ $subject->name }}</flux:link>
                .
            @elseif ($subject)
                <flux:text inline variant="strong">{{ $subject->name }}</flux:text>
                .
            @else
                <flux:text inline variant="strong">a user</flux:text>
                .
            @endif

            @break
        @case(Action::MemberRemoved)
            removed

            @if ($subject && $getUserSpaceUrl($subject))
                <flux:link wire:navigate :href="$getUserSpaceUrl($subject)">{{ $subject->name }}</flux:link>
            @elseif ($subject)
                <flux:text inline variant="strong">{{ $subject->name }}</flux:text>
            @else
                <flux:text inline variant="strong">a user</flux:text>
            @endif
            from the space.

            @break
        @case(Action::RepositoryCreated)
            created the repository

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject)">
                    {{ $subject->name }}
                </flux:link>
                .
            @else
                <flux:text inline variant="strong">a repository</flux:text>
                .
            @endif

            @break
        @case(Action::RepositoryUpdated)
            updated the repository

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject)">
                    {{ $subject->name }}
                </flux:link>
                .
            @else
                <flux:text inline variant="strong">a repository</flux:text>
                .
            @endif

            @break
        @case(Action::RepositoryDeleted)
            deleted a repository.

            @break
        @case(Action::RepositoryTransferred)
            transferred the repository

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject)">
                    {{ $subject->name }}
                </flux:link>
                .
            @else
                <flux:text inline variant="strong">a repository</flux:text>
                .
            @endif

            @break
        @case(Action::WebhookCreated)
            configured a webhook
            @if ($showRepository)
                in

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::WebhookUpdated)
            updated a webhook
            @if ($showRepository)
                in

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::WebhookDeleted)
            deleted a webhook
            @if ($showRepository)
                from

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::ManifestPushed)
            pushed a new manifest
            @if ($showRepository)
                to

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::ManifestPulled)
            pulled a manifest
            @if ($showRepository)
                from

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::ManifestDeleted)
            deleted a manifest
            @if ($showRepository)
                from

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::TagPushed)
            pushed the tag

            @if ($subject)
                <flux:link wire:navigate :href="route('app.space.repositories.tags', $subject->repository)">
                    {{ $subject->name }}
                </flux:link>
            @else
                <flux:text inline variant="strong">a tag</flux:text>
            @endif

            @if ($showRepository)
                to

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::TagUpdated)
            updated a tag
            @if ($showRepository)
                in

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @case(Action::TagDeleted)
            deleted a tag
            @if ($showRepository)
                from

                @if ($subject && $subject->repository)
                    <flux:link wire:navigate :href="route('app.space.repositories.overview', $subject->repository)">
                        {{ $subject->repository->name }}
                    </flux:link>
                @else
                    <flux:text inline variant="strong">a repository</flux:text>
                @endif
            @endif

            .

            @break
        @default
            {{ lcfirst($activity->description ?? "performed an action.") }}
    @endswitch
</flux:text>
