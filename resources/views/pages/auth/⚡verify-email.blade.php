<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component {
    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('app.dashboard'), navigate: true);
            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('status', __('A new verification link has been sent to your email address.'));
    }
};
?>

<x-slot:header>
    <div class="mt-auto flex items-center gap-8">
        <div>
            <x-auth.header :title="__('Verify your email')"
                           :description="__('Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.')" />

            <!-- Session Status -->
            <x-auth.session-status class="text-center" :status="session('status')" />
        </div>
    </div>
</x-slot:header>

<div class="flex flex-col gap-6">
    <flux:button variant="primary" wire:click="resend" class="w-full" data-test="resend-verification-button">
        {{ __('Resend verification email') }}
    </flux:button>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Wrong email?') }}</span>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm text-zinc-600 dark:text-zinc-400 underline cursor-pointer">
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>
