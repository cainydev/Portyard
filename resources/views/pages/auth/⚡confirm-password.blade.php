<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout("layouts.auth")] class extends Component {
    //
};
?>

<x-slot:header>
    <div class="mt-auto flex items-center gap-8">
        <div>
            <x-auth.header
                :title="__('Confirm password')"
                :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
            />

            <!-- Session Status -->
            <x-auth.session-status
                class="text-center"
                :status="session('status')"
            />
        </div>
    </div>
</x-slot>

<div class="flex flex-col gap-6">
    <form
        method="POST"
        action="{{ route("password.confirm.store") }}"
        class="flex flex-col gap-6"
    >
        @csrf

        <flux:input
            name="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="current-password"
            :placeholder="__('Password')"
            viewable
        />

        <flux:button
            variant="primary"
            type="submit"
            class="w-full"
            data-test="confirm-password-button"
        >
            {{ __("Confirm") }}
        </flux:button>
    </form>
</div>

<x-slot:footer>
    <div class="flex flex-col gap-4"></div>
</x-slot>
