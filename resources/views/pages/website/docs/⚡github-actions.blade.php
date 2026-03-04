<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};
?>

<x-layouts.docs>
    <flux:heading level="1" size="xl">GitHub Actions</flux:heading>
    <flux:subheading class="mt-2">Automate your container builds and pushes with GitHub Actions.</flux:subheading>

    <div class="mt-8 space-y-8">
        <div>
            <flux:heading level="2" size="lg">Setup Secrets</flux:heading>
            <flux:text class="mt-2">
                Add the following secrets to your GitHub repository under
                <strong>Settings &rarr; Secrets and variables &rarr; Actions</strong>:
            </flux:text>
            <ul class="mt-3 list-disc list-inside space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                <li><code class="font-mono">PORTYARD_USERNAME</code> &mdash; Your Portyard username</li>
                <li><code class="font-mono">PORTYARD_PASSWORD</code> &mdash; Your Portyard password</li>
            </ul>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Example Workflow</flux:heading>
            <flux:text class="mt-2">
                Create a file at <code class="font-mono">.github/workflows/deploy.yml</code> in your repository:
            </flux:text>
            <div class="mt-3 rounded-lg bg-zinc-100 dark:bg-zinc-900 p-4 font-mono text-sm overflow-x-auto whitespace-pre">name: Build and Push

on:
  push:
    branches: [main]

env:
  REGISTRY: {{ config('app.domain') }}
  IMAGE: john/app

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Log in to Portyard
        run: |
          echo "$&#123;&#123; secrets.PORTYARD_PASSWORD &#125;&#125;" | \
            docker login $REGISTRY \
              -u "$&#123;&#123; secrets.PORTYARD_USERNAME &#125;&#125;" \
              --password-stdin

      - name: Build and push
        run: |
          docker build -t $REGISTRY/$IMAGE:$&#123;&#123; github.sha &#125;&#125; .
          docker build -t $REGISTRY/$IMAGE:latest .
          docker push $REGISTRY/$IMAGE --all-tags</div>
        </div>

        <div class="-mx-6 lg:-mx-8 border-b border-stitched"></div>

        <div>
            <flux:heading level="2" size="lg">Tip</flux:heading>
            <flux:text class="mt-2">
                Tag images with both the commit SHA and <code class="font-mono">latest</code> so you can always roll back to a specific build.
            </flux:text>
        </div>
    </div>
</x-layouts.docs>
