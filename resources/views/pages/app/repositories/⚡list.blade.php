<?php

use App\Enums\Roles;
use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container class="p-0">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Repositories</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('Your Repositories')"
                              :subtitle="__('These are all the repositories you created - either manually or by pushing a tag.')">
            <flux:button :href="route('app.repositories.new')" icon="plus" wire:navigate.hover>
                New Repository
            </flux:button>
        </x-app.section-header>

        <div class="border-t border-stitched">
            <flux:table class="border-stitched">
                <flux:table.columns>
                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">Name</flux:table.column>
                    <flux:table.column class="border-stitched">Visibility</flux:table.column>
                    <flux:table.column class="border-stitched">Last pushed</flux:table.column>
                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse(auth()->user()->repositories()->wherePivot('role', Roles::Owner)->paginate() as $repo)
                        <flux:table.row
                            class="hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20">
                            <flux:table.cell class="border-stitched first:ps-6 lg:first:ps-8">
                                <div class="flex items-center gap-0.5">
                                    <flux:text variant="subtle">{{ auth()->user()->namespace }}</flux:text>
                                    <flux:text>/</flux:text>
                                    <flux:text>{{ $repo->name }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                @if($repo->public)
                                    <flux:icon name="globe-alt" class="stroke-green-500"/>
                                @else
                                    <flux:icon name="lock-closed" class="stroke-blue-500"/>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched">
                                <flux:text variant="subtle">No recent pushes</flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="border-stitched w-px last:pe-6 lg:last:pe-8">
                                <flux:button :href="route('app.repositories.overview', [$repo->namespace, $repo->name])"
                                             wire:navigate.hover
                                             variant="outline"
                                             size="sm"
                                             icon-trailing="arrow-right">
                                    Manage
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="first:ps-6 lg:first:ps-8 last:pe-6 lg:last:pe-8">
                                <flux:text
                                    variant="subtle">{{ __('You don\'t have any repositories yet.') }}</flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-container>

    <x-container class="p-0">
        <div class="px-6 lg:px-8 pt-6 lg:pt-8">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Collaborations</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-app.section-header class="p-6 lg:p-8"
                              :title="__('Other Repositories')"
                              :subtitle="__('These are all the repositories you were added to as a collaborator. Read more about roles here.')">
        </x-app.section-header>

        <div class="border-t border-stitched">
            <flux:table class="border-stitched">
                <flux:table.columns>
                    <flux:table.column class="border-stitched first:ps-6 lg:first:ps-8">Name</flux:table.column>
                    <flux:table.column class="border-stitched">Visibility</flux:table.column>
                    <flux:table.column class="border-stitched">Last pushed</flux:table.column>
                    <flux:table.column class="border-stitched last:pe-6 lg:last:pe-8 w-px"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse(auth()->user()->repositories()->wherePivotIn('role', [Roles::Maintainer, Roles::Developer, Roles::Viewer])->paginate() as $repo)
                        <flux:table.row
                            class="border-stitched  hover:bg-zinc-50/20 hover:dark:bg-zinc-700/20">
                            <flux:table.cell class="first:ps-6 lg:first:ps-8">
                                <div class="flex items-center gap-0.5">
                                    <flux:text variant="subtle">{{ $repo->namespace }}</flux:text>
                                    <flux:text>/</flux:text>
                                    <flux:text>{{ $repo->name }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($repo->public)
                                    <flux:icon name="globe-alt" class="stroke-green-500"/>
                                @else
                                    <flux:icon name="lock-closed" class="stroke-blue-500"/>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text variant="subtle">No recent pushes</flux:text>
                            </flux:table.cell>
                            <flux:table.cell class="w-px last:pe-6 lg:last:pe-8">
                                <flux:button :href="route('app.repositories.overview', [$repo->namespace, $repo->name])"
                                             wire:navigate.hover
                                             variant="outline"
                                             size="sm"
                                             icon-trailing="arrow-right">
                                    Manage
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="first:ps-6 lg:first:ps-8 last:pe-6 lg:last:pe-8">
                                <flux:text
                                    variant="subtle">{{ __('You have no collaborations yet.') }}</flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-container>
</div>
