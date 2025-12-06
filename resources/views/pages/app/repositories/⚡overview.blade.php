<?php

use App\Models\Repository;
use Livewire\Component;

new class extends Component {
    public Repository $repository;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
    }
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Repositories</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $repository->namespace }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $repository->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </x-container>
</div>
