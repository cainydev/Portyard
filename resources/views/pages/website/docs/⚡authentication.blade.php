<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<x-layouts.docs>
    <flux:heading level="1" size="xl">Authentication</flux:heading>
    <flux:subheading class="mt-2">How to authenticate with your Portyard registry.</flux:subheading>

    <div class="mt-8 space-y-8">
        <div>
            <flux:heading level="2" size="lg">Docker Login</flux:heading>
            <flux:text class="mt-2">
                Use the standard <code class="font-mono">docker login</code> command with your Portyard username and password.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker login {{ config('app.domain') }}</code>
            </div>
            <flux:text class="mt-2">
                You will be prompted for your username and password. Use the same credentials you use to log in to the Portyard web interface.
            </flux:text>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Non-Interactive Login</flux:heading>
            <flux:text class="mt-2">
                For CI/CD pipelines, pass your credentials directly. Use <code class="font-mono">--password-stdin</code> to avoid exposing your password in shell history.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>echo "$PORTYARD_PASSWORD" | docker login {{ config('app.domain') }} -u "$PORTYARD_USERNAME" --password-stdin</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Podman</flux:heading>
            <flux:text class="mt-2">
                Portyard is fully OCI-compliant and works with Podman out of the box.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>podman login {{ config('app.domain') }}</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Access Tokens</flux:heading>
            <flux:text class="mt-2">
                Granular access tokens with scoped permissions are coming soon. For now, use your account credentials to authenticate.
            </flux:text>
        </div>
    </div>
</x-layouts.docs>
