<?php

namespace App\Listeners;

use App\Contracts\TrackableEvent;
use App\Enums\Action;
use App\Events\Repository\RepositoryCreated;
use App\Events\Repository\RepositoryDeleted;
use App\Events\Repository\RepositoryTransferred;
use App\Events\Repository\RepositoryUpdated;
use App\Events\Space\MemberAccepted;
use App\Events\Space\MemberDeclined;
use App\Events\Space\MemberInvited;
use App\Events\Space\MemberRemoved;
use App\Events\Space\MemberRoleUpdated;
use App\Events\Space\SpaceCreated;
use App\Events\Space\SpaceDeleted;
use App\Events\Space\SpaceUpdated;
use App\Events\User\UserUpdated;
use App\Events\Webhook\WebhookCreated;
use App\Events\Webhook\WebhookDeleted;
use App\Events\Webhook\WebhookUpdated;
use App\Models\Activity;
use App\Models\Repository;
use App\Models\User;
use Cainy\Dockhand\Events\ManifestDeletedEvent;
use Cainy\Dockhand\Events\ManifestPulledEvent;
use Cainy\Dockhand\Events\ManifestPushedEvent;
use Cainy\Dockhand\Events\RegistryBaseEvent;
use Cainy\Dockhand\Events\RegistryEvent;
use Cainy\Dockhand\Events\RepoDeletedEvent;
use Cainy\Dockhand\Events\TagDeletedEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;

class ActivitySubscriber
{
    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            // 1. Native Laravel Events
            Login::class => 'handleLogin',
            Registered::class => 'handleRegistration',

            // 2. Application Events
            SpaceCreated::class => 'handleAppEvent',
            SpaceUpdated::class => 'handleAppEvent',
            SpaceDeleted::class => 'handleAppEvent',
            MemberInvited::class => 'handleAppEvent',
            MemberAccepted::class => 'handleAppEvent',
            MemberDeclined::class => 'handleAppEvent',
            MemberRoleUpdated::class => 'handleAppEvent',
            MemberRemoved::class => 'handleAppEvent',
            UserUpdated::class => 'handleAppEvent',
            RepositoryCreated::class => 'handleAppEvent',
            RepositoryUpdated::class => 'handleAppEvent',
            RepositoryDeleted::class => 'handleAppEvent',
            RepositoryTransferred::class => 'handleAppEvent',
            WebhookCreated::class => 'handleAppEvent',
            WebhookUpdated::class => 'handleAppEvent',
            WebhookDeleted::class => 'handleAppEvent',

            // 3. Dockhand Registry Events
            ManifestPushedEvent::class => 'handleRegistryEvent',
            ManifestPulledEvent::class => 'handleRegistryEvent',
            ManifestDeletedEvent::class => 'handleRegistryEvent',
            TagDeletedEvent::class => 'handleRegistryEvent',
            RepoDeletedEvent::class => 'handleRegistryEvent',
        ];
    }

    /**
     * Handler for all Application Events implementing TrackableEvent.
     */
    public function handleAppEvent(TrackableEvent $event): void
    {
        Activity::create([
            'request_id' => null,
            'space_id' => $event->spaceId(),
            'user_id' => auth()->id() ?? $event->actor?->id,
            'subject_type' => $event->subject()?->getMorphClass(),
            'subject_id' => $event->subject()?->getKey(),
            'action' => $event->action(),
            'description' => $event->action()->label(),
            'metadata' => $event->properties(),
        ]);
    }

    /**
     * Adapter for Native Login Event.
     */
    public function handleLogin(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Activity::create([
            'request_id' => null,
            'space_id' => null,
            'user_id' => $user->id,
            'subject_type' => $user->getMorphClass(),
            'subject_id' => $user->getKey(),
            'action' => Action::UserLoggedIn,
            'description' => Action::UserLoggedIn->label(),
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }

    /**
     * Adapter for Native Registration Event.
     */
    public function handleRegistration(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Activity::create([
            'request_id' => null,
            'space_id' => null,
            'user_id' => $user->id,
            'subject_type' => $user->getMorphClass(),
            'subject_id' => $user->getKey(),
            'action' => Action::UserRegistered,
            'description' => Action::UserRegistered->label(),
            'metadata' => [
                'ip' => request()->ip(),
            ],
        ]);
    }

    /**
     * Handler for Dockhand Registry Events.
     */
    public function handleRegistryEvent(RegistryBaseEvent $event): void
    {
        $action = match ($event::class) {
            ManifestPushedEvent::class => Action::ManifestPushed,
            ManifestPulledEvent::class => Action::ManifestPulled,
            ManifestDeletedEvent::class => Action::ManifestDeleted,
            TagDeletedEvent::class => Action::TagDeleted,
            RepoDeletedEvent::class => Action::RepositoryDeleted,
            default => null,
        };

        if (! $action) {
            return;
        }

        try {
            $repo = Repository::fromPath($event->targetRepository);
        } catch (\Exception) {
            return;
        }

        $user = $event->actorName ? User::where('name', $event->actorName)->first() : null;

        $tag = null;
        $mediaType = null;

        if ($event instanceof RegistryEvent) {
            $tag = $event->targetTag;
            $mediaType = $event->targetMediaType?->value;
        }

        $desc = $action->label();
        if ($tag) {
            $desc .= " {$tag}";
        } elseif ($action === Action::RepositoryDeleted) {
            $desc .= " {$event->targetRepository}";
        }

        Activity::create([
            'request_id' => $event->requestId,
            'space_id' => $repo->space_id,
            'user_id' => $user?->id,
            'subject_type' => $repo->getMorphClass(),
            'subject_id' => $repo->getKey(),
            'action' => $action,
            'description' => $desc,
            'metadata' => [
                'digest' => $event->targetDigest,
                'tag' => $tag,
                'media_type' => $mediaType,
                'ip' => $event->requestAddr,
                'agent' => $event->requestUserAgent,
            ],
        ]);
    }
}
