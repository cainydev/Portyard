<?php

use App\Models\Space;
use App\Enums\Roles;
use App\Rules\ValidUsername;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public string $name = '';
    public string $namespace = '';
    public string $description = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'namespace' => ['required', 'string', 'max:39', 'unique:spaces,namespace', new ValidUsername],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updatedName($value): void
    {
        $this->namespace = Str::slug($value);
    }

    public function create(): void
    {
        if (auth()->user()->spaces()->count() >= Space::BETA_MAX_SPACES_PER_USER) {
            $this->addError('name', __('You have reached the maximum of :count spaces allowed during beta.', ['count' => Space::BETA_MAX_SPACES_PER_USER]));

            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {
            $space = Space::create([
                'name' => $this->name,
                'namespace' => $this->namespace,
                'description' => $this->description,
            ]);

            $space->users()->attach(auth()->user(), ['role' => Roles::Owner->value]);

            DB::commit();

            auth()->user()->switchSpace($space);

            \Flux\Flux::toast(__("Space created successfully."), duration: 2000, variant: "success");

            $this->redirect(route('app.space.dashboard', $space), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    #[\Livewire\Attributes\Computed]
    public function atSpaceLimit(): bool
    {
        return auth()->user()->spaces()->count() >= Space::BETA_MAX_SPACES_PER_USER;
    }

    public function render()
    {
        return $this->view()
            ->title(__('New Space'));
    }
};
?>

<div class="flex flex-col grow" x-data>
    <x-container class="flex flex-col grow p-0">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ __('Spaces') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('New Space') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('New Space')"
                              :subtitle="__('Create a new space to organize your repositories and collaborators.')">
            <flux:button :href="route('app.space.dashboard', auth()->user()->currentSpace())" icon="arrow-left" variant="subtle"
                         wire:navigate.hover>
                {{ __('Back to Dashboard') }}
            </flux:button>
        </x-app.section-header>

        @if ($this->atSpaceLimit)
            <div class="border-t border-stitched bg-amber-50 dark:bg-amber-950/30 p-6 lg:p-8 flex items-center gap-3">
                <flux:icon.exclamation-triangle class="text-amber-500 shrink-0" />
                <div>
                    <flux:heading size="sm">{{ __('Space limit reached') }}</flux:heading>
                    <flux:text variant="subtle" size="sm">{{ __('You have reached the maximum of :count spaces allowed during the beta. Please delete an existing space before creating a new one.', ['count' => \App\Models\Space::BETA_MAX_SPACES_PER_USER]) }}</flux:text>
                </div>
            </div>
        @endif

        <form wire:submit="create" class="border-t border-stitched grid lg:grid-cols-2 grow {{ $this->atSpaceLimit ? 'opacity-50 pointer-events-none' : '' }}">
            <!-- LEFT COLUMN: Input & Configuration -->
            <div class="flex flex-col gap-8">

                {{-- 1. Identity Section --}}
                <div class="space-y-6 lg:space-y-8 p-6 lg:p-8">
                    <flux:field>
                        <flux:label for="name">{{ __('Space Name') }}</flux:label>
                        <flux:input type="text"
                                    name="name"
                                    id="name"
                                    required
                                    wire:model.live="name"
                                    placeholder="My Awesome Team"/>
                        <flux:description>
                            {{ __('The display name for your space.') }}
                        </flux:description>
                        <flux:error name="name"/>
                        <flux:error name="namespace"/>
                    </flux:field>

                    <flux:field>
                        <flux:label for="description" badge="Optional">{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" name="description"
                                       placeholder="{{ __('What is this space used for?') }}" rows="3"/>
                        <flux:error name="description"/>
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                        {{ __('Create Space') }}
                    </flux:button>
                </div>
            </div>

            <div class="flex flex-col gap-8 border-l border-stitched p-6 lg:p-8">
                {{-- 1. Visual URI Preview --}}
                <div class="space-y-4">
                    <flux:heading size="lg">{{ __('Space Preview') }}</flux:heading>
                    <flux:callout class="flux items-center">
                        <x-slot:icon>
                            <flux:icon.globe-alt class="text-zinc-400 shrink-0"/>
                        </x-slot:icon>
                        <flux:callout.heading
                            class="font-mono text-sm text-zinc-600 dark:text-zinc-400 truncate flex items-center gap-1">
                            <span>{{ config('app.domain') }}</span>
                            <span>/</span>
                            <span
                                class="text-zinc-900 dark:text-zinc-100 font-semibold"
                                x-text="$wire.namespace.replaceAll(' ', '-') || 'my-team'"></span>
                        </flux:callout.heading>
                    </flux:callout>
                </div>

                {{-- 2. Naming Rules --}}
                <div class="space-y-4">
                    <flux:heading size="lg">{{ __('Namespace Requirements') }}</flux:heading>
                    <flux:callout icon="information-circle" variant="info">
                        <ul class="text-sm list-disc list-inside space-y-1">
                            <li>{{ __('Lowercase alphanumeric characters only') }}</li>
                            <li>{{ __('Can contain hyphens (-)') }}</li>
                            <li>{{ __('Cannot start or end with a hyphen') }}</li>
                            <li>{{ __('Maximum 39 characters') }}</li>
                            <li>{{ __('Must be unique across Portyard') }}</li>
                        </ul>
                    </flux:callout>
                </div>

                {{-- 3. Info Callout --}}
                <flux:text variant="subtle">
                    {{ __('Spaces help you group related repositories and manage access for your team. You can invite collaborators and assign roles after creating the space.') }}
                </flux:text>
            </div>
        </form>
    </x-container>
</div>
