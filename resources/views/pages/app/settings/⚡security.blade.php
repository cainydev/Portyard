<?php

use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

new #[Title('Security')] class extends Component {
    // Password Update State
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Two-Factor Authentication State
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $show2faWizard = false;
    public bool $show2faDisable = false;
    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public string $disableCode = '';

    // Browser Sessions State
    public string $sessionPassword = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        // Initialize 2FA State
        if (Features::enabled(Features::twoFactorAuthentication())) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdateUserPassword $updater): void
    {
        $updater->update(auth()->user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        \Flux\Flux::toast(__("Password updated successfully."), duration: 2000, variant: "success");
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enableTwoFactor(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->load2faSetupData();

        $this->show2faWizard = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function load2faSetupData(): void
    {
        $user = auth()->user();

        try {
            $svg = $user?->twoFactorQrCodeSvg();
            // Make SVG transparent and use currentColor
            $svg = str_replace('<rect x="0" y="0" width="192" height="192" fill="#ffffff"/>', '', $svg);
            $svg = str_replace('fill="#2d3748"', 'fill="currentColor"', $svg);

            $this->qrCodeSvg = $svg;
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function show2faVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;
            $this->resetErrorBag();
            return;
        }

        $this->cancel2faSetup();
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validateOnly('code');

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->twoFactorEnabled = true;

        $this->cancel2faSetup();

        \Flux\Flux::toast(__('Two-factor authentication has been enabled.'), variant: 'success');
    }

    /**
     * Initiate the 2FA disablement process.
     */
    public function initiate2faDisable(): void
    {
        $this->show2faDisable = true;
        $this->reset('disableCode');
        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disableTwoFactor(DisableTwoFactorAuthentication $disableTwoFactorAuthentication, \Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider $provider): void
    {
        $this->validate([
            'disableCode' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();

        if (empty($user->two_factor_secret) ||
            !$provider->verify(decrypt($user->two_factor_secret), $this->disableCode)) {
            $this->addError('disableCode', __('The provided two-factor authentication code was invalid.'));
            return;
        }

        $disableTwoFactorAuthentication($user);

        $this->twoFactorEnabled = false;
        $this->cancel2faDisable();

        \Flux\Flux::toast(__('Two-factor authentication has been disabled.'), variant: 'success');
    }

    /**
     * Cancel the two-factor authentication setup.
     */
    public function cancel2faSetup(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'show2faWizard',
            'showVerificationStep',
        );

        $this->resetErrorBag();

        if (!$this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Cancel the 2FA disablement process.
     */
    public function cancel2faDisable(): void
    {
        $this->reset('show2faDisable', 'disableCode');
        $this->resetErrorBag();
    }

    /**
     * Reset 2FA verification state.
     */
    public function reset2faVerification(): void
    {
        $this->reset('code', 'showVerificationStep');
        $this->resetErrorBag();
    }

    /**
     * Log out from other browser sessions.
     */
    public function logoutOtherBrowserSessions(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $this->validate([
            'sessionPassword' => ['required', 'current_password:web'],
        ]);

        auth()->guard()->logoutOtherDevices($this->sessionPassword);

        $this->deleteOtherSessionRecords();

        $this->reset('sessionPassword');

        $this->dispatch('close-modal', name: 'logout-sessions-modal');

        \Flux\Flux::toast(__('Logged out of other browser sessions.'), variant: 'success');
    }

    /**
     * Delete the other session records from storage.
     */
    protected function deleteOtherSessionRecords(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', auth()->user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    /**
     * Get the current sessions for the user.
     */
    public function getSessionsProperty(): array
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        return DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', auth()->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object)[
                    'agent' => $this->createAgent($session->user_agent),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Create a new agent instance from the given user agent.
     */
    protected function createAgent($userAgent): object
    {
        $os = 'Unknown OS';
        $browser = 'Unknown Browser';

        if (str_contains($userAgent, 'Windows')) $os = 'Windows';
        elseif (str_contains($userAgent, 'Macintosh')) $os = 'macOS';
        elseif (str_contains($userAgent, 'Linux')) $os = 'Linux';
        elseif (str_contains($userAgent, 'Android')) $os = 'Android';
        elseif (str_contains($userAgent, 'iPhone')) $os = 'iOS';

        if (str_contains($userAgent, 'Chrome')) $browser = 'Chrome';
        elseif (str_contains($userAgent, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($userAgent, 'Safari')) $browser = 'Safari';
        elseif (str_contains($userAgent, 'Edge')) $browser = 'Edge';

        return (object)[
            'os' => $os,
            'browser' => $browser,
            'is_desktop' => !str_contains($userAgent, 'Mobile'),
        ];
    }

    /**
     * Get the current 2FA wizard configuration state.
     */
    public function getTwoFactorWizardConfigProperty(): array
    {
        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify Authentication Code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Confirm & Enable'),
            ];
        }

        return [
            'title' => __('Enable Two-Factor Authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
}; ?>

<x-layouts.user-settings>
    <x-container inset>
        <div class="grid grid-cols-1 lg:grid-cols-2 border-b border-stitched">
            {{-- 1. Password Section --}}
            <x-app.settings.section
                :title="__('Update Password')"
                :subtitle="__('Ensure your account is using a long, random password to stay secure.')"
                class="lg:border-b-0! lg:border-r border-stitched">
                <flux:input
                    :label="__('Current Password')"
                    type="password"
                    wire:model="current_password"
                    class="w-full" />

                <flux:input
                    :label="__('New Password')"
                    type="password"
                    wire:model="password"
                    class="w-full" />

                <flux:input
                    :label="__('Confirm Password')"
                    type="password"
                    wire:model="password_confirmation"
                    class="w-full" />

                <flux:button class="w-full sm:w-auto" wire:click.prevent="updatePassword">Update Password</flux:button>
                </x-app.settings.section>

            {{-- 2. Two Factor Authentication Section --}}
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
                <x-app.settings.section
                    :title="__('Two Factor Authentication')"
                    :subtitle="__('Add additional security to your account using two factor authentication.')"
                    class="border-b-0!">

                    @if ($twoFactorEnabled)
                        @if ($show2faDisable)
                            {{-- Inline Disable 2FA Wizard --}}
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="md">{{ __('Disable 2FA?') }}</flux:heading>
                                    <flux:text class="mt-1">
                                        {{ __('Enter your 6-digit code to confirm disablement.') }}
                                    </flux:text>
                                </div>

                                <div
                                    class="flex items-center gap-2 sm:gap-4 p-4 sm:p-6 -mx-6 lg:-mx-8 bg-stone-50 dark:bg-stone-900/50 border-y border-stitched">
                                    <flux:button variant="ghost" size="sm" icon="x-mark"
                                                 wire:click="cancel2faDisable" />

                                    <div class="flex-1 flex justify-center scale-90 sm:scale-100">
                                        <flux:otp
                                            name="disableCode"
                                            wire:model="disableCode"
                                            length="6"
                                            label="OTP Code"
                                            label:sr-only
                                            class="mx-auto"
                                            x-init="$nextTick(() => $el.querySelector('input').focus())"
                                        />
                                    </div>

                                    <flux:button
                                        variant="danger"
                                        size="sm"
                                        x-bind:disabled="$wire.disableCode.length < 6"
                                        wire:click="disableTwoFactor"
                                    >
                                        {{ __('Disable') }}
                                    </flux:button>
                                </div>
                                <flux:error name="disableCode" />
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <flux:badge color="green">{{ __('Enabled') }}</flux:badge>
                                </div>

                                <flux:text>
                                    {{ __('With two-factor authentication enabled, you will be prompted for a secure pin during login.') }}
                                </flux:text>

                                <div class="flex justify-start">
                                    <flux:button
                                        variant="danger"
                                        icon="shield-exclamation"
                                        icon:variant="outline"
                                        wire:click="initiate2faDisable"
                                        class="w-full sm:w-auto"
                                    >
                                        {{ __('Disable 2FA') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endif
                    @elseif ($show2faWizard)
                        {{-- 2FA Setup Wizard --}}
                        <div class="space-y-6">
                            @if ($showVerificationStep)
                                <div>
                                    <flux:heading size="md">{{ __('Verify Code') }}</flux:heading>
                                    <flux:text
                                        class="mt-1">{{ __('Enter the 6-digit code from your app.') }}</flux:text>
                                </div>

                                <div
                                    class="flex items-center gap-2 sm:gap-4 p-4 sm:p-6 -mx-6 lg:-mx-8 bg-stone-50 dark:bg-stone-900/50 border-y border-stitched">
                                    <flux:button variant="ghost" size="sm" icon="chevron-left"
                                                 wire:click="reset2faVerification" />

                                    <div class="flex-1 flex justify-center scale-90 sm:scale-100">
                                        <flux:otp
                                            name="code"
                                            wire:model="code"
                                            length="6"
                                            label="OTP Code"
                                            label:sr-only
                                            class="mx-auto"
                                            x-init="$nextTick(() => $el.querySelector('input').focus())"
                                        />
                                    </div>

                                    <flux:button
                                        variant="primary"
                                        size="sm"
                                        x-bind:disabled="$wire.code.length < 6"
                                        wire:click="confirmTwoFactor"
                                    >
                                        {{ __('Confirm') }}
                                    </flux:button>
                                </div>
                            @else
                                <div>
                                    <flux:heading size="md">{{ __('Enable 2FA') }}</flux:heading>
                                    <flux:text class="mt-1">{{ __('Scan the QR code or enter the key.') }}</flux:text>
                                </div>

                                @error('setupData')
                                <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}" />
                                @enderror

                                <div class="relative w-48 aspect-square text-stone-900 dark:text-stone-100">
                                    @empty($qrCodeSvg)
                                        <div class="absolute inset-0 flex items-center justify-center animate-pulse">
                                            <flux:icon.loading />
                                        </div>
                                    @else
                                        {!! $qrCodeSvg !!}
                                    @endempty
                                </div>

                                <div class="space-y-4">
                                    <flux:input
                                        label="{{ __('Setup Key') }}"
                                        readonly
                                        value="{{ $manualSetupKey }}"
                                        class="w-full"
                                        copyable
                                    />

                                    <div class="flex items-center space-x-3">
                                        <flux:button
                                            variant="outline"
                                            class="flex-1"
                                            wire:click="cancel2faSetup"
                                        >
                                            {{ __('Cancel') }}
                                        </flux:button>

                                        <flux:button
                                            :disabled="$errors->has('setupData')"
                                            variant="primary"
                                            class="flex-1"
                                            wire:click="show2faVerificationIfNecessary"
                                        >
                                            {{ __('Continue') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <flux:badge color="red">{{ __('Disabled') }}</flux:badge>
                            </div>

                            <flux:text variant="subtle">
                                {{ __('Enable two-factor authentication to secure your account with a pin retrieved from your phone.') }}
                            </flux:text>

                            <flux:button
                                variant="primary"
                                icon="shield-check"
                                icon:variant="outline"
                                wire:click="enableTwoFactor"
                                class="w-full sm:w-auto"
                            >
                                {{ __('Enable 2FA') }}
                            </flux:button>
                        </div>
                    @endif
                </x-app.settings.section>
            @endif
        </div>

        {{-- 3. Browser Sessions Section --}}
        @if (config('session.driver') === 'database')
            <x-app.settings.section
                :title="__('Browser Sessions')"
                :subtitle="__('Manage and log out your active sessions on other browsers and devices.')"
                class="pb-0! lg:pb-0!">
                <x-slot:actions>
                    <flux:modal.trigger name="logout-sessions-modal">
                        <flux:button variant="outline" size="sm">
                            {{ __('Log Out Other Sessions') }}
                        </flux:button>
                    </flux:modal.trigger>
                </x-slot:actions>

                <flux:text class="max-w-2xl">
                    {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
                </flux:text>

                @if (count($this->sessions) > 0)
                    <div class="-mx-6 lg:-mx-8 border-t border-stitched">
                        <flux:table class="border-stitched">
                            <flux:table.columns>
                                <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">{{ __('Device') }}</flux:table.column>
                                <flux:table.column class="border-stitched">{{ __('IP Address') }}</flux:table.column>
                                <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8">{{ __('Last Active') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach ($this->sessions as $session)
                                    <flux:table.row>
                                        <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                            <div class="flex items-center gap-3">
                                                @if ($session->agent->is_desktop)
                                                    <flux:icon.computer-desktop class="size-4 text-stone-400" />
                                                @else
                                                    <flux:icon.device-phone-mobile class="size-4 text-stone-400" />
                                                @endif

                                                <div class="text-sm font-medium">
                                                    {{ $session->agent->os }} - {{ $session->agent->browser }}
                                                </div>
                                            </div>
                                        </flux:table.cell>

                                        <flux:table.cell class="border-stitched">
                                            <flux:text size="sm">{{ $session->ip_address }}</flux:text>
                                        </flux:table.cell>

                                        <flux:table.cell class="border-stitched last:pe-6 lg:last:pe-8">
                                            @if ($session->is_current_device)
                                                <flux:badge color="green" size="sm" inset="left">{{ __('This device') }}</flux:badge>
                                            @else
                                                <flux:text size="sm">{{ $session->last_active }}</flux:text>
                                            @endif
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif

                <flux:modal name="logout-sessions-modal" class="min-w-[22rem] max-w-lg">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Log Out Other Browser Sessions') }}</flux:heading>
                            <flux:text class="mt-2">
                                {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                            </flux:text>
                        </div>

                        <flux:input
                            type="password"
                            placeholder="{{ __('Password') }}"
                            wire:model="sessionPassword"
                            wire:keydown.enter="logoutOtherBrowserSessions" />

                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                            </flux:modal.close>

                            <flux:button
                                variant="primary"
                                wire:click="logoutOtherBrowserSessions"
                            >
                                {{ __('Log Out Other Browser Sessions') }}
                            </flux:button>
                        </div>
                    </div>
                </flux:modal>
            </x-app.settings.section>
        @endif
    </x-container>
</x-layouts.user-settings>
