<?php

use App\Models\Repository;
use App\Rules\ValidRepositoryName;
use App\Services\NamingService;
use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public string $name = '';
    public string $description = '';

    public string $visibility = 'private';
    public bool $immutable = false;
    public bool $scanOnPush = true;

    public string $namespace = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', new ValidRepositoryName],
            'description' => 'nullable|string|max:255',
        ];
    }

    public function mount(): void
    {
        $this->namespace = auth()->user()->namespace;
    }

    public function updated(): void
    {
        $this->validate();
    }

    public function create(): void
    {
        $this->validate();

        DB::beginTransaction();

        $repository = Repository::create([
            'namespace' => $this->namespace,
            'name' => $this->name,
            'description' => $this->description,
            'public' => $this->visibility === 'public',
        ]);

        DB::commit();

        $this->redirect(route('app.repositories.overview', [$this->namespace, $repository->name]), navigate: true);
    }
};
?>

<div class="flex flex-col grow" x-data>
    <x-container class="flex flex-col grow p-0">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Repositories</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>New</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('New Repository')"
                              :subtitle="__('Create a new repository to start pushing your images.')">
            <flux:button :href="route('app.repositories.list')" icon="arrow-left" variant="subtle" wire:navigate.hover>
                Back to Repositories
            </flux:button>
        </x-app.section-header>

        <form wire:submit="create" class="border-t border-stitched grid lg:grid-cols-2 grow">
            <!-- LEFT COLUMN: Input & Configuration -->
            <div class="flex flex-col gap-8">

                {{-- 1. Identity Section --}}
                <div class="space-y-6 lg:space-y-8 p-6 lg:p-8">
                    <flux:field>
                        <flux:label for="name">Repository Name</flux:label>
                        <flux:input type="text" name="name" id="name" required wire:model.live="name"
                                    :prefix="$namespace . '/'"
                                    placeholder="my-app"
                        />
                        <flux:error name="name"/>
                        <flux:description>
                            Great repository names are short and memorable.
                        </flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label for="description" badge="Optional">Description</flux:label>
                        <flux:textarea wire:model="description" name="description"
                                       placeholder="What is this repository used for?" rows="3"/>
                        <flux:error name="description"/>
                    </flux:field>

                    {{-- 2. Visibility Section --}}
                    <flux:radio.group wire:model="visibility" label="Visibility" variant="cards" :indicator="false"
                                      class="max-sm:flex-col">
                        <flux:radio value="private" icon="lock-closed" label="Private"
                                    description="You choose who can see and commit to this repository."/>

                        <flux:radio value="public" icon="globe-alt" label="Public"
                                    description="Anyone on the internet can see this repository."/>
                    </flux:radio.group>
                </div>

                {{-- 3. Advanced Settings (Fills the space nicely) --}}
                <div class="space-y-6 lg:space-y-8 border-b lg:border-t lg:border-b-0 border-stitched p-6 lg:p-8">
                    <flux:switch wire:model="immutable" label="Immutable Tags"
                                 description="Prevent image tags from being overwritten. Useful for production repositories to ensure consistency."/>

                    <flux:switch wire:model="scanOnPush" label="Scan on Push"
                                 description="Automatically scan uploaded images for security vulnerabilities."/>

                    <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                        Create Repository
                    </flux:button>
                </div>
            </div>

            <div class="flex flex-col gap-8 border-l border-stitched p-6 lg:p-8">
                {{-- 1. Visual URI Preview --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Repository Preview</flux:heading>
                    <flux:callout class="flux items-center">
                        <x-slot:icon>
                            <flux:icon.lock-closed x-show="$wire.visibility === 'private'"
                                                   class="text-zinc-400 shrink-0"/>
                            <flux:icon.globe-alt x-show="$wire.visibility === 'public'" class="text-zinc-400 shrink-0"/>
                        </x-slot:icon>
                        <flux:callout.heading
                            class="font-mono text-sm text-zinc-600 dark:text-zinc-400 truncate flex items-center gap-1">
                            <span>portyard.de</span>
                            <span>/</span>
                            <span
                                class="text-zinc-900 dark:text-zinc-100 font-semibold">{{ $namespace }}</span>
                            <span>/</span>
                            <span
                                class="text-zinc-900 dark:text-zinc-100 font-semibold"
                                x-text="$wire.name.replaceAll(' ', '-') || 'my-app'"></span>
                        </flux:callout.heading>
                    </flux:callout>
                </div>

                {{-- 2. Quick Commands --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg">Pushing your first image</flux:heading>
                        <flux:badge size="sm" icon="command-line">CLI</flux:badge>
                    </div>

                    <x-terminal class="shadow-sm">
                        <x-terminal-command>
                            <x-slot:comment>1. Login to the registry</x-slot:comment>
                            <x-slot:command>docker login portyard.de</x-slot:command>
                        </x-terminal-command>

                        <x-terminal-command>
                            <x-slot:comment>2. Tag your local image</x-slot:comment>
                            <x-slot:command>
                                docker tag my-image:latest <span class="break-all">portyard.de/{{ $namespace }}/<span
                                        x-text="$wire.name.length ? $wire.name : 'my-app'"></span>:latest</span>
                            </x-slot:command>
                        </x-terminal-command>

                        <x-terminal-command>
                            <x-slot:command>
                                <span class="text-zinc-500"># 3. Push to Portyard</span>
                                <br>docker push <span class="break-all">portyard.de/{{ $namespace }}/<span
                                        x-text="$wire.name.length ? $wire.name : 'my-app'"></span>:latest</span>
                            </x-slot:command>
                        </x-terminal-command>
                    </x-terminal>
                </div>

                {{-- 3. Naming Rules --}}
                <flux:callout icon="information-circle" variant="info">
                    <flux:callout.heading>Naming Requirements</flux:callout.heading>
                    <ul class="text-sm list-disc list-inside space-y-1">
                        <li>Lowercase alphanumeric characters only</li>
                        <li>Can contain hyphens (-), underscores (_), and dots (.)</li>
                        <li>Cannot start or end with a separator</li>
                        <li>No consecutive separators (e.g. ..)</li>
                    </ul>
                </flux:callout>
            </div>
        </form>
    </x-container>
</div>
