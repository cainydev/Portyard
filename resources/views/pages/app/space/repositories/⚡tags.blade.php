<?php

use App\Models\Repository;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public Repository $repository;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
    }

    #[Computed]
    public function tags()
    {
        return $this->repository->tags()->paginate(9);
    }

    public function render()
    {
        return $this->view()->title(__("Tags") . " · {$this->repository->path}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container inset border-bottom="center">
        <div class="grid md:grid-cols-3 -mr-px -mb-px">
            @foreach ($this->tags as $tag)
                <div
                    class="p-4 lg:p-6 group flex flex-col items-start justify-start rounded-none border-b border-r border-stitched">
                    <flux:heading size="xl">{{ $tag->name }}</flux:heading>

                    <div class="flex items-center flex-wrap gap-2 mt-4">
                        @if ($tag->manifest->isManifestList())
                            @foreach ($tag->manifest->childManifestEntries as $entry)
                                <flux:button href="#" size="sm">{{ $entry->platform_architecture }}</flux:button>
                            @endforeach
                        @else
                            <flux:button href="#" size="sm">
                                {{ $tag->imageConfig?->architecture ?? __("Unknown") }}
                            </flux:button>
                        @endif
                    </div>

                    <p
                        class="text-2xl mt-8 font-mono opacity-0 group-hover:opacity-30 delay-100 transition-opacity truncate w-full">
                        {{ $tag->manifest->isImageManifest() ? __("Manifest") : __("Manifest List") }}
                    </p>
                    <p
                        class="text-2xl cursor-pointer font-mono opacity-0 group-hover:opacity-40 transition-opacity duration-[400ms] truncate w-full"
                        x-on:click="
                            navigator.clipboard.writeText(@js($tag->manifest->digest)).then(() =>
                                $flux.toast({
                                    text: 'Digest copied!',
                                    variant: 'success',
                                    duration: 2000,
                                }),
                            )
                        ">
                        {{ $tag->manifest->digest }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-container>

    @if ($this->tags->hasPages())
        <x-container inset class="px-6 lg:px-8" border-bottom="center">
            {{ $this->tags->links() }}
        </x-container>
        <x-container></x-container>
    @endif
</x-layouts.repository>
