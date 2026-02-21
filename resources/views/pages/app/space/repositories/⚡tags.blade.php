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
            ->title(__('Tags') . " · {$this->repository->path}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container inset>
        <div class="grid md:grid-cols-3 -mr-px -mb-px">
            @foreach($repository->tags()->paginate(12) as $tag)
                <div
                    class="p-4 lg:p-6 group flex flex-col items-start justify-start rounded-none border-b border-r border-stitched">
                    <flux:heading size="xl">{{ $tag->name }}</flux:heading>

                    <div class="flex items-center flex-wrap gap-2 mt-4">
                        @if($tag->manifest->isManifestList())
                            @foreach($tag->manifest->childManifestEntries as $entry)
                                <flux:button href="#"
                                             size="sm">{{ $entry->platform_architecture }}</flux:button>
                            @endforeach
                        @else
                            <flux:button href="#"
                                         size="sm">{{ $tag->imageConfig?->architecture ?? __('Unknown') }}</flux:button>
                        @endif
                    </div>

                    <p class="text-2xl mt-4 font-mono opacity-0 group-hover:opacity-30 delay-100 transition-opacity truncate w-full">
                        {{ $tag->manifest->isImageManifest() ? __('Manifest') : __('Manifest List') }}
                    </p>
                    <p class="text-2xl cursor-pointer font-mono opacity-0 group-hover:opacity-40 transition-opacity duration-[400ms] truncate w-full"
                       x-on:click="navigator.clipboard.writeText(@js($tag->manifest->digest)).then(() => $flux.toast({ text: 'Digest copied!', variant: 'success', duration: 2000 }))">
                        {{ $tag->manifest->digest }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-container>
</x-layouts.repository>
