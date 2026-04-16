<?php

use App\Models\Repository;
use App\Rules\ValidRepositoryName;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public Repository $repository;

    public string $name;
    public ?string $description;
    public ?string $overview;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
        $this->name = $repository->name;
        $this->description = $repository->description;
        $this->overview = $repository->overview;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', new ValidRepositoryName],
            'description' => 'nullable|string|max:1000',
            'overview' => 'nullable|string|max:50000',
        ];
    }

    public function render()
    {
        return $this->view()
            ->title(__('Settings') . " · {$this->repository->path}");
    }

    public function save(): void
    {
        $this->authorize('manageSettings', $this->repository);

        $this->repository->update($this->validate());

        \Flux\Flux::toast("The repository {$this->repository->path} was successfully updated.", 'Repository updated', 2000, 'success');
    }

    public function deleteRepository(): void
    {
        $this->authorize('delete', $this->repository);

        DB::transaction(function () {
            $this->repository->tags->each->delete();
            $this->repository->delete();
        });

        \Flux\Flux::toast("The repository {$this->repository->path} was successfully deleted.", 'Repository deleted', 2000, 'success');

        $this->redirect(route('app.space.repositories.list'));
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container inset>
        @if(auth()->user()->can('manageSettings', $repository))
            <x-app.settings.section :title="__('General')"
                                    :subtitle="__('Update the basic settings for this repository.')">
                <x-slot:actions>
                    <flux:button variant="primary" wire:click="save">
                        {{ __('Save Changes') }}
                    </flux:button>
                </x-slot:actions>

                <flux:field class="max-w-lg">
                    <flux:label for="name">Repository Name</flux:label>
                    <flux:input type="text"
                                name="name"
                                id="name"
                                required
                                wire:model.live="name"
                                placeholder="my-app"/>
                    <flux:description>
                        Great repository names are short and memorable.
                    </flux:description>
                    <flux:error name="name"/>
                </flux:field>

                <flux:field>
                    <flux:label for="description">{{ __('Description') }}</flux:label>
                    <flux:input name="description"
                                id="description"
                                wire:model.live="description"
                                :placeholder="__('A short description of this repository.')"
                                rows="5"/>
                    <flux:description>
                        Let others know what this repository is about.
                    </flux:description>
                    <flux:error name="description"/>
                </flux:field>

                <flux:field>
                    <flux:label for="overview">{{ __('Overview') }}</flux:label>
                    <flux:textarea name="overview"
                                   id="overview"
                                   wire:model.live="overview"
                                   :placeholder="__('A README of this repository. You can use Markdown in here!')"
                                   rows="5"/>
                    <flux:description>
                        Help others understand the purpose and usage of this repository by providing a detailed
                        overview.
                    </flux:description>
                    <flux:error name="overview"/>
                </flux:field>
            </x-app.settings.section>
        @endif

        <x-app.settings.section :subtitle="__('Handle with care! These actions are destructive.')">
            <x-slot:title>
                <span class="flex items-center gap-2">
                <flux:heading>{{ __('Danger Zone') }}</flux:heading>
                <flux:icon icon="exclamation-triangle" color="orange" variant="mini"/>
                </span>
            </x-slot:title>

            @if(auth()->user()->can('delete', $repository))
                <flux:field class="flex flex-col items-start">
                    <flux:label>{{ __('Delete this repository') }}</flux:label>
                    <flux:description>{{ __('Once you delete a repository, there is no going back. Please be certain.') }}</flux:description>
                    <flux:modal.trigger name="delete-profile">
                        <flux:button variant="danger">Delete</flux:button>
                    </flux:modal.trigger>
                </flux:field>

                <flux:modal name="delete-profile" class="min-w-[22rem] max-w-lg">
                    <div class="space-y-6" x-data="{ confirmation: '' }">
                        <div>
                            <flux:heading size="lg">{{ __('Delete this repository?') }}</flux:heading>
                            <flux:text class="mt-2">
                                You're about to delete this repository.<br>
                                This action cannot be reversed. To confirm, please type the repository name
                                <strong>{{ $repository->name }}</strong> below:
                            </flux:text>
                        </div>

                        <flux:input x-model="confirmation" :placeholder="$repository->name"/>

                        <div class="flex gap-2">
                            <flux:spacer/>
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>

                            <flux:button variant="danger"
                                         x-bind:disabled="confirmation.trim() !== '{{ $repository->name }}'"
                                         wire:click="deleteRepository">
                                {{ __('Delete repository') }}
                            </flux:button>
                        </div>
                    </div>
                </flux:modal>
            @endif
        </x-app.settings.section>
    </x-container>
</x-layouts.repository>
