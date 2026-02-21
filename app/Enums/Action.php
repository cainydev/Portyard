<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Action: string
{
    use EnumToArray;

    // --- Auth & User ---
    case UserLoggedIn = 'auth.login';
    case UserRegistered = 'auth.register';
    case UserUpdated = 'auth.updated';
    case UserPasswordUpdated = 'auth.password_updated';

    // --- Space / Organization ---
    case SpaceCreated = 'space.created';
    case SpaceUpdated = 'space.updated';
    case SpaceDeleted = 'space.deleted';

    // --- Members ---
    case MemberInvited = 'member.invited';
    case MemberAccepted = 'member.joined';
    case MemberDeclined = 'member.invite_declined';
    case MemberRoleUpdated = 'member.role_updated';
    case MemberRemoved = 'member.removed';

    // --- Repository ---
    case RepositoryCreated = 'repository.created';
    case RepositoryUpdated = 'repository.updated';
    case RepositoryDeleted = 'repository.deleted';
    case RepositoryTransferred = 'repository.transferred';

    // --- Webhooks ---
    case WebhookCreated = 'webhook.created';
    case WebhookUpdated = 'webhook.updated';
    case WebhookDeleted = 'webhook.deleted';

    // --- Registry ---
    case ManifestPushed = 'registry.manifest_pushed';
    case ManifestPulled = 'registry.manifest_pulled';
    case ManifestDeleted = 'registry.manifest_deleted';
    case TagPushed = 'registry.tag_pushed';
    case TagUpdated = 'registry.tag_updated';
    case TagDeleted = 'registry.tag_deleted';

    public function label(): string
    {
        return match ($this) {
            // Auth & User
            Action::UserLoggedIn => 'User Logged In',
            Action::UserRegistered => 'User Registered',
            Action::UserUpdated => 'User Updated',
            Action::UserPasswordUpdated => 'User Password Updated',

            // Space / Organization
            Action::SpaceCreated => 'Space Created',
            Action::SpaceUpdated => 'Space Updated',
            Action::SpaceDeleted => 'Space Deleted',

            // Members
            Action::MemberInvited => 'Member Invited',
            Action::MemberAccepted => 'Member Accepted Invitation',
            Action::MemberDeclined => 'Member Declined Invitation',
            Action::MemberRoleUpdated => 'Member Role Updated',
            Action::MemberRemoved => 'Member Removed',

            // Repository
            Action::RepositoryCreated => 'Repository Created',
            Action::RepositoryUpdated => 'Repository Updated',
            Action::RepositoryDeleted => 'Repository Deleted',
            Action::RepositoryTransferred => 'Repository Transferred',

            // Webhooks
            Action::WebhookCreated => 'Webhook Created',
            Action::WebhookUpdated => 'Webhook Updated',
            Action::WebhookDeleted => 'Webhook Deleted',

            // Registry
            Action::ManifestPushed => 'Manifest Pushed',
            Action::ManifestPulled => 'Manifest Pulled',
            Action::ManifestDeleted => 'Manifest Deleted',
            Action::TagPushed => 'Tag Pushed',
            Action::TagUpdated => 'Tag Updated',
            Action::TagDeleted => 'Tag Deleted',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            // Auth & User
            Action::UserLoggedIn,
            Action::UserRegistered,
            Action::UserUpdated,
            Action::UserPasswordUpdated => 'user',

            // Space / Organization
            Action::SpaceCreated,
            Action::SpaceUpdated,
            Action::SpaceDeleted => 'folder',

            // Members
            Action::MemberInvited,
            Action::MemberAccepted,
            Action::MemberDeclined => 'envelope',
            Action::MemberRoleUpdated,
            Action::MemberRemoved => 'users',

            // Repository
            Action::RepositoryCreated,
            Action::RepositoryUpdated,
            Action::RepositoryDeleted,
            Action::RepositoryTransferred => 'circle-stack',

            // Webhooks
            Action::WebhookCreated,
            Action::WebhookUpdated,
            Action::WebhookDeleted => 'bolt',

            // Registry
            Action::ManifestPushed => 'document-arrow-up',
            Action::ManifestPulled => 'document-arrow-down',
            Action::ManifestDeleted => 'document-minus',
            Action::TagPushed,
            Action::TagDeleted,
            Action::TagUpdated => 'tag'
        };
    }

    public function color(): string
    {
        return match ($this) {
            // Auth & User
            Action::UserLoggedIn,
            Action::UserRegistered,
            Action::UserUpdated,
            Action::UserPasswordUpdated => 'blue',

            // Space / Organization
            Action::SpaceCreated,
            Action::SpaceUpdated,
            Action::SpaceDeleted => 'purple',

            // Members
            Action::MemberInvited,
            Action::MemberAccepted,
            Action::MemberDeclined => 'teal',
            Action::MemberRoleUpdated,
            Action::MemberRemoved => 'indigo',

            // Repository
            Action::RepositoryCreated,
            Action::RepositoryUpdated,
            Action::RepositoryDeleted,
            Action::RepositoryTransferred => 'cyan',

            // Webhooks
            Action::WebhookCreated,
            Action::WebhookUpdated,
            Action::WebhookDeleted => 'yellow',

            // Registry
            Action::ManifestPushed => 'green',
            Action::ManifestPulled => 'gray',
            Action::ManifestDeleted => 'red',
            Action::TagPushed,
            Action::TagDeleted,
            Action::TagUpdated => 'orange'
        };
    }
}
