<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title("Your Profile")] class extends Component {
    public string $name;
    public string $email;

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function save(UpdateUserProfileInformation $updater): void
    {
        $updater->update(auth()->user(), [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        \Flux\Flux::toast(__("Profile updated successfully."), duration: 2000, variant: "success");
    }

    public function deleteAccount(): void
    {
        $user = auth()->user();

        auth()->logout();

        $user->delete();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(route('website.home'));
    }
};
?>

<x-layouts.user-settings>
    <x-container inset>
        <x-app.settings.section
            :title="__('Profile Information')"
            :subtitle="__('Update your name and email address.')">
            <flux:input
                :label="__('Name')"
                wire:model="name"
                class="max-w-md" />

            <flux:input
                :label="__('Email Address')"
                type="email"
                wire:model="email"
                class="max-w-md" />

            <flux:button class="max-w-md" wire:click.prevent="save">Save Changes</flux:button>
        </x-app.settings.section>

        <x-app.settings.section :subtitle="__('Handle with care! These actions are destructive.')">
            <x-slot:title>
                <span class="flex items-center gap-2">
                <flux:heading>{{ __('Danger Zone') }}</flux:heading>
                <flux:icon icon="exclamation-triangle" color="orange" variant="mini"/>
                </span>
            </x-slot:title>

            <flux:field class="flex flex-col items-start">
                <flux:label>{{ __('Delete your account') }}</flux:label>
                <flux:description>{{ __('Once you delete your account, there is no going back. All of your spaces, repositories and data will be permanently deleted. Please be certain.') }}</flux:description>
                <flux:modal.trigger name="delete-account">
                    <flux:button variant="danger">Delete Account</flux:button>
                </flux:modal.trigger>
            </flux:field>

            <flux:modal name="delete-account" class="min-w-[22rem] max-w-lg">
                <div class="space-y-6" x-data="{ confirmation: '' }">
                    <div>
                        <flux:heading size="lg">{{ __('Delete your account?') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('You are about to permanently delete your account.') }}<br>
                            {{ __('This action cannot be reversed. To confirm, please type your name') }}
                            <strong>{{ auth()->user()->name }}</strong> {{ __('below:') }}
                        </flux:text>
                    </div>

                    <flux:input x-model="confirmation" :placeholder="auth()->user()->name" />

                    <div class="flex gap-2">
                        <flux:spacer/>
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>

                        <flux:button variant="danger"
                                     x-bind:disabled="confirmation.trim() !== '{{ auth()->user()->name }}'"
                                     wire:click="deleteAccount">
                            {{ __('Delete Account') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
        </x-app.settings.section>
    </x-container>
</x-layouts.user-settings>
