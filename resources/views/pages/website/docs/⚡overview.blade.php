<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<x-layouts.docs>
    <flux:heading level="1" size="xl">Getting Started</flux:heading>
    <flux:subheading class="mt-2">Get up and running with Portyard in under a minute.</flux:subheading>

    <div class="mt-8 space-y-8">
        <div>
            <flux:heading level="2" size="lg">1. Create an account</flux:heading>
            <flux:text class="mt-2">
                @guest
                    <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 underline">Sign up</a>
                    for a free account. No credit card required during the open beta.
                @endguest
                @auth
                    You're already signed in. Head to your
                    <a href="{{ route('root') }}" class="text-blue-600 dark:text-blue-400 underline">dashboard</a>
                    to get started.
                @endauth
            </flux:text>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">2. Log in with Docker</flux:heading>
            <flux:text class="mt-2">
                Authenticate your Docker client with your Portyard credentials.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker login {{ config('app.domain') }}</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">3. Tag your image</flux:heading>
            <flux:text class="mt-2">
                Tag an existing image with your Portyard registry URL, space name, and repository name.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker tag app:latest {{ config('app.domain') }}/john/app:latest</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">4. Push your image</flux:heading>
            <flux:text class="mt-2">
                Push the tagged image to your Portyard registry.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker push {{ config('app.domain') }}/john/app:latest</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">5. You're all set!</flux:heading>
            <flux:text class="mt-2">
                Your image is now stored on GDPR-compliant German infrastructure. Pull it from any server
                using <code class="font-mono">docker pull</code>.
            </flux:text>
        </div>
    </div>
</x-layouts.docs>
