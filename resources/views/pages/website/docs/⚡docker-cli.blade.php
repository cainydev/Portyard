<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<x-layouts.docs>
    <flux:heading level="1" size="xl">Docker CLI</flux:heading>
    <flux:subheading class="mt-2">Common Docker and Podman commands for working with Portyard.</flux:subheading>

    <div class="mt-8 space-y-8">
        <div>
            <flux:heading level="2" size="lg">Login</flux:heading>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker login {{ config('app.domain') }}</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Tag an Image</flux:heading>
            <flux:text class="mt-2">
                Tag a local image with your registry URL, space, and repository name.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker tag app:latest {{ config('app.domain') }}/my-space/app:latest</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Push an Image</flux:heading>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker push {{ config('app.domain') }}/my-space/app:latest</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Pull an Image</flux:heading>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker pull {{ config('app.domain') }}/my-space/app:latest</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Push All Tags</flux:heading>
            <flux:text class="mt-2">
                Push every tag for a given image in one command.
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto">
                <code>docker push {{ config('app.domain') }}/john/app --all-tags</code>
            </div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Podman Compatible</flux:heading>
            <flux:text class="mt-2">
                Portyard is fully OCI-compliant. Replace <code class="font-mono">docker</code> with
                <code class="font-mono">podman</code> in any of the commands above and they will work identically.
            </flux:text>
        </div>
    </div>
</x-layouts.docs>
