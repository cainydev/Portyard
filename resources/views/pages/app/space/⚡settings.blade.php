<?php

use App\Enums\Roles;
use App\Events\Space\MemberInvited;
use App\Events\Space\MemberRemoved;
use App\Events\Space\MemberRoleUpdated;
use App\Mail\SpaceInvitationMail;
use App\Models\Invitation;
use App\Models\Repository;
use App\Models\Space;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

new class extends Component {
    public Space $space;

    public string $name;
    public ?string $description = null;

    // Invite form state
    public string $inviteEmail = '';
    public string $inviteRole = 'viewer';

    protected array $rules = [
        "name" => "required|string|max:255",
        "description" => "nullable|string|max:1000",
    ];

    public function mount(Space $space): void
    {
        $this->space = $space;
        $this->name = $space->name;
        $this->description = $space->description;
    }

    public function save(): void
    {
        $this->authorize('update', $this->space);

        $this->space->update([
            "name" => $this->name,
            "description" => $this->description,
        ]);

        \Flux\Flux::toast(__("Space settings updated successfully."), duration: 2000, variant: "success");
    }

    public function inviteMember(): void
    {
        $this->authorize('invite', $this->space);

        $this->validate([
            'inviteEmail' => 'required|email|max:255',
            'inviteRole' => 'required|in:' . implode(',', Roles::values()),
        ]);

        if ($this->space->users()->where('email', $this->inviteEmail)->exists()) {
            $this->addError('inviteEmail', __('This user is already a member of this space.'));
            return;
        }

        if (Invitation::query()->where('space_id', $this->space->id)->where('email', $this->inviteEmail)->pending()->exists()) {
            $this->addError('inviteEmail', __('An invitation has already been sent to this email.'));
            return;
        }

        $invitation = Invitation::create([
            'space_id' => $this->space->id,
            'email' => $this->inviteEmail,
            'role' => $this->inviteRole,
            'invited_by' => auth()->id(),
        ]);

        Mail::to($this->inviteEmail)->send(new SpaceInvitationMail($invitation));

        MemberInvited::dispatch($this->space, $this->inviteEmail, $this->inviteRole, auth()->user());

        $this->reset('inviteEmail', 'inviteRole');
        $this->inviteRole = 'viewer';

        \Flux\Flux::toast(__('Invitation sent successfully.'), duration: 2000, variant: 'success');
    }

    public function updateRole(string $memberId, string $role): void
    {
        $this->authorize('manageMembers', $this->space);

        $pivot = $this->space->users()->newPivotQuery()->where('id', $memberId)->firstOrFail();

        if ($pivot->user_id === auth()->id()) {
            \Flux\Flux::toast(__('You cannot change your own role.'), duration: 2000, variant: 'danger');
            return;
        }

        if ($pivot->role === Roles::Owner->value && $this->space->owners()->count() <= 1) {
            \Flux\Flux::toast(__('Cannot change role of the last owner.'), duration: 2000, variant: 'danger');
            return;
        }

        $member = \App\Models\User::findOrFail($pivot->user_id);

        $this->space->users()->updateExistingPivot($member->id, ['role' => $role]);

        MemberRoleUpdated::dispatch($this->space, $member, $role, auth()->user());

        \Flux\Flux::toast(__('Member role updated.'), duration: 2000, variant: 'success');
    }

    public function removeMember(string $memberId): void
    {
        $this->authorize('manageMembers', $this->space);

        $pivot = $this->space->users()->newPivotQuery()->where('id', $memberId)->firstOrFail();

        if ($pivot->user_id === auth()->id()) {
            \Flux\Flux::toast(__('You cannot remove yourself.'), duration: 2000, variant: 'danger');
            return;
        }

        if ($pivot->role === Roles::Owner->value && $this->space->owners()->count() <= 1) {
            \Flux\Flux::toast(__('Cannot remove the last owner.'), duration: 2000, variant: 'danger');
            return;
        }

        $member = \App\Models\User::findOrFail($pivot->user_id);

        $this->space->users()->detach($member->id);

        MemberRemoved::dispatch($this->space, $member, auth()->user());

        \Flux\Flux::toast(__('Member removed.'), duration: 2000, variant: 'success');
    }

    public function cancelInvitation(string $invitationId): void
    {
        $this->authorize('invite', $this->space);

        Invitation::query()
            ->where('id', $invitationId)
            ->where('space_id', $this->space->id)
            ->pending()
            ->firstOrFail()
            ->delete();

        \Flux\Flux::toast(__('Invitation cancelled.'), duration: 2000, variant: 'success');
    }

    public function deleteSpace(): void
    {
        $this->authorize('delete', $this->space);

        DB::transaction(function () {
            $this->space->repositories->each(function (Repository $repo) {
                $repo->tags->each->delete();
                $repo->delete();
            });

            $this->space->delete();
        });

        \Flux\Flux::toast(__('The space was successfully deleted.'), __('Space deleted'), 2000, 'success');

        $this->redirect(route('root'));
    }

    public function render()
    {
        return $this->view()->title(__("Space Settings"));
    }
};
?>

<div class="flex flex-col grow" x-data>
    <x-container class="flex flex-col grow p-0">
        {{-- Header --}}
        <flux:breadcrumbs class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs.item>{{ $space->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __("Settings") }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-app.section-header
            class="p-6 lg:p-8"
            :title="__('Space Settings')"
            :subtitle="__('Manage space members, repository defaults, billing and more.')"></x-app.section-header>

        <x-app.tabs>
            {{-- The Tab Navigation Buttons --}}
            <x-slot:tabs>
                <flux:tab name="general" icon="building-office">General</flux:tab>
                <flux:tab name="access" icon="users">Members & Robots</flux:tab>
                <flux:tab name="repositories" icon="cube">Repositories</flux:tab>
                <flux:tab name="lifecycle" icon="trash">Lifecycle</flux:tab>
                {{-- <flux:tab name="billing" icon="banknotes">Billing</flux:tab> --}}
            </x-slot>

            {{-- 1. General --}}
            <flux:tab.panel name="general">
                <x-app.settings.section
                    :title="__('Storage Usage')"
                    :subtitle="__('Current storage consumption for this space.')">
                    <x-app.storage-bar :space="$space" class="max-w-md" />
                </x-app.settings.section>

                <x-app.settings.section
                    :title="__('General Settings')"
                    :subtitle="__('Update the basic information and settings for this space.')">
                    <flux:input
                        :label="__('Space Namespace')"
                        :description="__('The unique namespace used in repository paths.')"
                        :value="$space->namespace"
                        class="max-w-md"
                        disabled />

                    <flux:input
                        :label="__('Space Name')"
                        :description="__('The name of the space as it appears in the UI.')"
                        wire:model="name"
                        class="max-w-md" />

                    <flux:textarea
                        :label="__('Space Description')"
                        :description="__('A brief description of the space.')"
                        wire:model="description"
                        class="max-w-md"
                        rows="3" />

                    <flux:button class="mt-4 max-w-md" wire:click.prevent="save">Save Changes</flux:button>
                </x-app.settings.section>

                @can('delete', $space)
                    <x-app.settings.section :subtitle="__('Handle with care! These actions are destructive.')">
                        <x-slot:title>
                            <span class="flex items-center gap-2">
                                <flux:heading>{{ __('Danger Zone') }}</flux:heading>
                                <flux:icon icon="exclamation-triangle" color="orange" variant="mini"/>
                            </span>
                        </x-slot:title>

                        <flux:field class="flex flex-col items-start">
                            <flux:label>{{ __('Delete this space') }}</flux:label>
                            <flux:description>{{ __('Once you delete a space, all repositories and their data will be permanently deleted. This action cannot be undone.') }}</flux:description>
                            <flux:modal.trigger name="delete-space">
                                <flux:button variant="danger">{{ __('Delete') }}</flux:button>
                            </flux:modal.trigger>
                        </flux:field>

                        <flux:modal name="delete-space" class="min-w-[22rem] max-w-lg">
                            <div class="space-y-6" x-data="{ confirmation: '' }">
                                <div>
                                    <flux:heading size="lg">{{ __('Delete this space?') }}</flux:heading>
                                    <flux:text class="mt-2">
                                        {{ __("You're about to delete this space and all of its repositories.") }}<br>
                                        {{ __('This action cannot be reversed. To confirm, please type the space namespace') }}
                                        <strong>{{ $space->namespace }}</strong> {{ __('below:') }}
                                    </flux:text>
                                </div>

                                <flux:input x-model="confirmation" :placeholder="$space->namespace"/>

                                <div class="flex gap-2">
                                    <flux:spacer/>
                                    <flux:modal.close>
                                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                    </flux:modal.close>

                                    <flux:button variant="danger"
                                                 x-bind:disabled="confirmation.trim() !== '{{ $space->namespace }}'"
                                                 wire:click="deleteSpace">
                                        {{ __('Delete space') }}
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                    </x-app.settings.section>
                @endcan
            </flux:tab.panel>

            {{-- 2. Access (Members) --}}
            <flux:tab.panel name="access">
                {{-- Invite Form --}}
                @can('invite', $space)
                    <x-app.settings.section
                        :title="__('Invite Member')"
                        :subtitle="__('Send an invitation email to add a new member to this space.')">
                        <div class="flex flex-col sm:flex-row gap-3 max-w-xl">
                            <flux:input
                                type="email"
                                wire:model="inviteEmail"
                                :placeholder="__('Email address')"
                                class="flex-1" />

                            <flux:select variant="listbox" wire:model="inviteRole" class="max-w-fit">
                                <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
                                <flux:select.option value="developer">{{ __('Developer') }}</flux:select.option>
                                <flux:select.option value="maintainer">{{ __('Maintainer') }}</flux:select.option>
                                <flux:select.option value="owner">{{ __('Owner') }}</flux:select.option>
                            </flux:select>

                            <flux:button wire:click="inviteMember" icon="paper-airplane">
                                {{ __('Send Invite') }}
                            </flux:button>
                        </div>

                        <flux:error name="inviteEmail" />
                    </x-app.settings.section>
                @endcan

                {{-- Members Table --}}
                <x-app.settings.section
                    :title="__('Members')"
                    :subtitle="__('People who have access to this space.')">
                </x-app.settings.section>

                <x-app.settings.section class="p-0!">
                    <flux:table class="border-stitched">
                        <flux:table.columns>
                            <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">{{ __('User') }}</flux:table.column>
                            <flux:table.column class="border-stitched">{{ __('Role') }}</flux:table.column>
                            <flux:table.column class="border-stitched">{{ __('Joined') }}</flux:table.column>
                            @can('manageMembers', $space)
                                <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px">{{ __('Actions') }}</flux:table.column>
                            @endcan
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($space->users()->withPivot(['id', 'role', 'created_at'])->get() as $member)
                                <flux:table.row wire:key="member-{{ $member->pivot->id }}">
                                    <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                        <div class="flex flex-col">
                                            <flux:text>{{ $member->name }}</flux:text>
                                            <flux:text variant="subtle" size="sm">{{ $member->email }}</flux:text>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell class="border-stitched">
                                        <flux:badge size="sm" class="capitalize">{{ $member->pivot->role }}</flux:badge>
                                    </flux:table.cell>

                                    <flux:table.cell class="border-stitched">
                                        <flux:text variant="subtle">{{ $member->pivot->created_at->diffForHumans() }}</flux:text>
                                    </flux:table.cell>

                                    @can('manageMembers', $space)
                                        <flux:table.cell class="border-stitched last:pe-6 lg:last:pe-8">
                                            @if ($member->id !== auth()->id())
                                                @php
                                                    $isLastOwner = $member->pivot->role === \App\Enums\Roles::Owner->value && $space->owners()->count() <= 1;
                                                @endphp

                                                <div class="flex items-center gap-2">
                                                    @unless ($isLastOwner)
                                                        <flux:select
                                                            variant="listbox"
                                                            size="sm"
                                                            class="max-w-fit"
                                                            wire:change="updateRole('{{ $member->pivot->id }}', $event.target.value)">
                                                            @foreach (\App\Enums\Roles::cases() as $role)
                                                                <flux:select.option
                                                                    value="{{ $role->value }}"
                                                                    :selected="$member->pivot->role === $role->value">
                                                                    {{ ucfirst($role->value) }}
                                                                </flux:select.option>
                                                            @endforeach
                                                        </flux:select>

                                                        <flux:button
                                                            variant="danger"
                                                            size="xs"
                                                            icon="x-mark"
                                                            wire:click="removeMember('{{ $member->pivot->id }}')"
                                                            wire:confirm="{{ __('Are you sure you want to remove this member?') }}" />
                                                    @else
                                                        <flux:text variant="subtle" size="sm">{{ __('Last owner') }}</flux:text>
                                                    @endunless
                                                </div>
                                            @else
                                                <flux:text variant="subtle" size="sm">{{ __('You') }}</flux:text>
                                            @endif
                                        </flux:table.cell>
                                    @endcan
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </x-app.settings.section>

                {{-- Pending Invitations --}}
                @can('invite', $space)
                    @php
                        $pendingInvitations = \App\Models\Invitation::query()
                            ->where('space_id', $space->id)
                            ->pending()
                            ->with('inviter')
                            ->latest()
                            ->get();
                    @endphp

                    @if ($pendingInvitations->isNotEmpty())
                        <x-app.settings.section
                            :title="__('Pending Invitations')"
                            :subtitle="__('Invitations that have not yet been accepted or declined.')">
                        </x-app.settings.section>

                        <x-app.settings.section class="p-0!">
                            <flux:table class="border-stitched">
                                <flux:table.columns>
                                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">{{ __('Email') }}</flux:table.column>
                                    <flux:table.column class="border-stitched">{{ __('Role') }}</flux:table.column>
                                    <flux:table.column class="border-stitched">{{ __('Invited By') }}</flux:table.column>
                                    <flux:table.column class="border-stitched">{{ __('Sent') }}</flux:table.column>
                                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px">{{ __('Actions') }}</flux:table.column>
                                </flux:table.columns>

                                <flux:table.rows>
                                    @foreach ($pendingInvitations as $invitation)
                                        <flux:table.row wire:key="invitation-{{ $invitation->id }}">
                                            <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                                <flux:text>{{ $invitation->email }}</flux:text>
                                            </flux:table.cell>

                                            <flux:table.cell class="border-stitched">
                                                <flux:badge size="sm" class="capitalize">{{ $invitation->role->value }}</flux:badge>
                                            </flux:table.cell>

                                            <flux:table.cell class="border-stitched">
                                                <flux:text variant="subtle">{{ $invitation->inviter?->name ?? __('Unknown') }}</flux:text>
                                            </flux:table.cell>

                                            <flux:table.cell class="border-stitched">
                                                <flux:text variant="subtle">{{ $invitation->created_at->diffForHumans() }}</flux:text>
                                            </flux:table.cell>

                                            <flux:table.cell class="border-stitched last:pe-6 lg:last:pe-8">
                                                <flux:button
                                                    variant="danger"
                                                    size="xs"
                                                    wire:click="cancelInvitation('{{ $invitation->id }}')"
                                                    wire:confirm="{{ __('Cancel this invitation?') }}">
                                                    {{ __('Cancel') }}
                                                </flux:button>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        </x-app.settings.section>
                    @endif
                @endcan
            </flux:tab.panel>

            {{-- 3. Repositories --}}
            <flux:tab.panel name="repositories">
                <x-app.settings.section
                    :title="__('Coming Soon')"
                    :subtitle="__('Repository settings will be available in a future release. These will allow you to configure default behaviors and policies for repositories within this space.')"></x-app.settings.section>
            </flux:tab.panel>

            {{-- 4. Lifecycle --}}
            <flux:tab.panel name="lifecycle">
                <x-app.settings.section
                    :title="__('Coming Soon')"
                    :subtitle="__('Lifecycle management features will be available in a future release. These will allow you to define policies for automatic cleanup of old or unused images to save storage space.')"></x-app.settings.section>
            </flux:tab.panel>

            {{-- 5. Billing (hidden during beta) --}}
        </x-app.tabs>
    </x-container>
</div>
