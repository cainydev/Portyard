<?php

use App\Enums\Roles;
use App\Facades\CurrentSpace;
use App\Livewire\Traits\InteractsWithSpace;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, InteractsWithSpace;

    #[Computed]
    public function repositories(): LengthAwarePaginator
    {
        return $this->currentSpace->repositories()->paginate(10);
    }

    public function render()
    {
        return $this->view()->title(__("Repositories"));
    }
};
?>

<div class="flex flex-col grow">
    <x-container class="p-0" border-bottom="center">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __("Repositories") }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header
            class="p-6 lg:p-8"
            :title="__('Space Repositories')"
            :subtitle="__('These are all the repositories that were created in this space - either manually or by pushing a tag.')">
            <flux:button :href="route('app.space.repositories.new')" icon="plus" wire:navigate.hover>
                {{ __("New Repository") }}
            </flux:button>
        </x-app.section-header>

        <div class="border-t border-stitched">
            <flux:table class="border-stitched">
                <flux:table.columns>
                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">
                        {{ __("Name") }}
                    </flux:table.column>
                    <flux:table.column class="border-stitched">{{ __("Visibility") }}</flux:table.column>
                    <flux:table.column class="border-stitched">{{ __("Last pushed") }}</flux:table.column>
                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->repositories as $repo)
                        <flux:table.row class="hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20">
                            <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                <div class="flex items-center gap-0.5 h-full">
                                    <flux:text variant="subtle">{{ $repo->space->namespace }}</flux:text>
                                    <flux:text>/</flux:text>
                                    <flux:text>{{ $repo->name }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <div class="flex items-center gap-2 h-full">
                                    @if ($repo->public)
                                        <flux:icon name="globe-alt" class="stroke-green-500" />
                                        <flux:text class="mt-px">{{ __("Public") }}</flux:text>
                                    @else
                                        <flux:icon name="lock-closed" class="stroke-blue-500" />
                                        <flux:text class="mt-px">{{ __("Private") }}</flux:text>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <div class="flex items-center gap-2 h-full">
                                    <flux:text variant="subtle">{{ __("No recent pushes") }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched w-px last:pe-6 lg:last:pe-8">
                                <div class="flex items-center gap-2 h-full">
                                    <flux:button
                                        :href="route('app.space.repositories.overview', [$repo->space, $repo])"
                                        wire:navigate.hover
                                        variant="outline"
                                        size="sm"
                                        icon-trailing="arrow-right">
                                        {{ __("Manage") }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="first:ps-6 lg:first:ps-8 last:pe-6 lg:last:pe-8">
                                <flux:text variant="subtle">
                                    {{ __('You don\'t have any repositories yet.') }}
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-container>

    @if ($this->repositories->hasPages())
        <x-container inset class="px-6 lg:px-8" border-bottom="center">
            {{ $this->repositories->links() }}
        </x-container>
        <x-container></x-container>
    @endif
</div>
