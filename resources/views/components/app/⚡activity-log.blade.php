<?php

use App\Models\Activity;
use App\Models\Space;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    /**
     * The space ID.
     */
    public Space $space;

    /**
     * The actions that should be displayed.
     */
    public array $actions = [];

    /**
     * The current page of the activity log.
     */
    public int $activityPage = 1;

    /**
     * Track if we have more pages so we don't query the DB unnecessarily.
     */
    public bool $hasMore = true;

    public function loadMoreActivities(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->activityPage++;
        unset($this->activities);
    }

    #[Computed]
    public function activities(): Paginator
    {
        if (! $this->hasMore) {
            return new Paginator([], 10, $this->activityPage);
        }

        $paginator = $this->space
            ->activities()
            ->with(["user", "subject"])
            ->whereIn("action", $this->actions)
            ->latest()
            ->simplePaginate(10, ["*"], "page", $this->activityPage);

        $this->hasMore = $paginator->hasMorePages();

        return $paginator;
    }
};
?>

<div>
    <ul role="list">
        @island(name: 'feed')
            @foreach ($this->activities as $activity)
                <li class="group grid grid-cols-[max-content_minmax(0,1fr)] gap-x-3">
                    {{-- Column 1: Icon & Line --}}
                    <div class="flex flex-col items-center">
                        <div
                            @class(["flex justify-center items-center rounded-full p-2 border border-stitched bg-white dark:bg-zinc-800 relative z-10"])>
                            <flux:icon :icon="$activity->action->icon()" variant="mini" />
                        </div>

                        {{-- Vertical connector line (automatically stretches perfectly to the bottom) --}}
                        <div class="border-stitched border-l-2 ml-px grow"></div>
                    </div>

                    {{-- Column 2: Content --}}
                    <div class="pb-10 pt-1.5">
                        <div class="flex min-w-0 flex-1 justify-between items-center space-x-4">
                            <x-app.activity :activity="$activity" />

                            <div
                                class="grid cursor-default text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                <time
                                    class="col-start-1 row-start-1 opacity-100 group-hover:opacity-0 transition-opacity"
                                    datetime="{{ $activity->created_at->toIso8601String() }}">
                                    {{ $activity->created_at->format("d M") }}
                                </time>

                                <time
                                    class="col-start-1 row-start-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ $activity->created_at->format("g:i A") }}
                                </time>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        @endisland

        {{-- Infinite Scroll Trigger --}}
        <div class="relative flex space-x-3" wire:intersect="loadMoreActivities" wire:island.append="feed">
            <div
                @class(["flex justify-center items-center rounded-full p-2 border border-stitched bg-white dark:bg-zinc-800"])>
                <flux:icon icon="variable" variant="mini" />
            </div>

            <div class="flex min-w-0 flex-1 justify-between items-center space-x-4">
                @php($options = ["The primordial soup.", "The dawn of recorded history.", "Someone plugged the server in.", "Nothing but dinosaurs beyond this point.", "The archives end here.", "Singularity.", "The big bang.", "Beginning of time.", "Humans discover computers."])

                <flux:text>
                    {{ __($options[abs(crc32($space->namespace)) % count($options)]) }}
                </flux:text>
            </div>
        </div>
    </ul>
</div>
