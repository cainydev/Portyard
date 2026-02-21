<?php

use App\Models\Repository;
use Livewire\Component;

new class extends Component {
    public Repository $repository;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
    }

    public function render()
    {
        return $this->view()
            ->title(__('Collaborators') . " · {$this->repository->path}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container>

    </x-container>
</x-layouts.repository>
