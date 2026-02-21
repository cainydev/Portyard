<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component {
    //
};
?>

@section('alpine', 'twoFactorAuth')

<x-slot:header>
    <div class="mt-auto flex items-center gap-8">
        <div>
            <div x-show="!showRecoveryInput">
                <x-auth.header
                    :title="__('Authentication Code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput" x-cloak>
                <x-auth.header
                    :title="__('Recovery Code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <x-auth.session-status class="text-center" :status="session('status')"/>
        </div>
    </div>
</x-slot:header>

<div class="flex flex-col gap-6">
    <div class="relative w-full h-auto">
        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="space-y-5 text-center">
                <div x-show="!showRecoveryInput">
                    <div class="flex items-center justify-center my-5">
                        <flux:otp
                            x-model="code"
                            length="6"
                            name="code"
                            label="OTP Code"
                            label:sr-only
                            class="mx-auto"
                        />
                    </div>
                </div>

                <div x-show="showRecoveryInput" x-cloak>
                    <div class="my-5">
                        <flux:input
                            type="text"
                            name="recovery_code"
                            x-ref="recovery_code"
                            x-bind:required="showRecoveryInput"
                            autocomplete="one-time-code"
                            x-model="recovery_code"
                        />
                    </div>

                    @error('recovery_code')
                    <flux:text color="red">
                        {{ $message }}
                    </flux:text>
                    @enderror
                </div>

                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full"
                >
                    {{ __('Continue') }}
                </flux:button>
            </div>

            <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                <span class="opacity-50">{{ __('or you can') }}</span>
                <div class="inline font-medium underline cursor-pointer opacity-80">
                    <span x-show="!showRecoveryInput"
                          @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                    <span x-show="showRecoveryInput" x-cloak
                          @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                </div>
            </div>
        </form>
    </div>
</div>

<x-slot:footer>
    <div class="flex flex-col gap-4"></div>
</x-slot:footer>

<script>
    Alpine.data('twoFactorAuth', () => ({
        showRecoveryInput: @js($errors->has('recovery_code')),
        code: '',
        recovery_code: '',

        toggleInput() {
            this.showRecoveryInput = !this.showRecoveryInput;
            this.code = '';
            this.recovery_code = '';

            this.$dispatch('clear-2fa-auth-code');

            this.$nextTick(() => {
                if (this.showRecoveryInput) {
                    this.$refs.recovery_code?.focus();
                } else {
                    this.$dispatch('focus-2fa-auth-code');
                }
            });
        }
    }))
</script>
