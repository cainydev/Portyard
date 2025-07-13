<div class="flex gap-12 justify-between flex-wrap">
    <div class="flex flex-col gap-2">
        <span class="flex items-center gap-2">
            <h2 class="text-3xl font-semibold">{{ $repository->path }}</h2>
            @if($repository->public)
                <x-tooltip text="Public">
                    <x-heroicon-s-globe-europe-africa class="w-8 h-8"/>
                </x-tooltip>
            @else
                <x-tooltip text="Private">
                    <x-heroicon-s-lock-closed class="w-8 h-8"/>
                </x-tooltip>
            @endif
        </span>
        <p class="text-gray-400">Created about {{ $repository->created_at->diffForHumans() }}</p>
    </div>
</div>
