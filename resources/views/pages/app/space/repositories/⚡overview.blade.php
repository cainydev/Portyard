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
            ->title($this->repository->path);
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container>
        <div
            class="prose prose-code:before:hidden prose-code:after:hidden prose-code:text-base prose-h1:text-2xl prose-h2:text-xl prose-h3:text-lg prose-h4:text-base dark:prose-invert">
            @if(str($repository->overview)->trim()->isEmpty())
                <x-app.section-header :title="__('No overview provided')"/>
            @else
                @markdown($repository->overview ?? __('No overview provided.'))
            @endif
        </div>
    </x-container>
</x-layouts.repository>
