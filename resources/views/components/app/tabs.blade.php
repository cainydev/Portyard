@props([
    'tabs' => null,
    'border' => true,
])

<div>
    @if($tabs === null || $tabs->isEmpty())
        <div {{ $attributes->class('flex items-stretch') }}>
            {{-- Navigation Bar --}}
            <flux:navbar :scrollable="true"
                         class="{{ $border ? 'border-y! border-stitched!' : '' }} px-6 lg:px-8 gap-6 lg:gap-8 py-2! [&>a]:data-current:after:-bottom-2!">
                {{ $slot }}
            </flux:navbar>

            {{-- Decorative Filler --}}
            <div class="border-l border-stitched bg-diag-lines flex-1"></div>
        </div>
    @else
        <flux:tab.group {{ $attributes->class('[&>[role=tabpanel]]:p-0 [&>div>div.absolute]:hidden') }}>
            {{-- Navigation Bar --}}
            <flux:tabs :scrollable="true"
                       class="border-y! border-stitched! h-11 gap-0! ps-6 lg:ps-8 gap-x-6! lg:gap-x-8!">
                {{ $tabs }}

                {{-- Decorative Filler --}}
                <div class="border-l border-stitched bg-diag-lines flex-1"></div>
            </flux:tabs>

            {{-- Content Panels --}}
            {{ $slot }}
        </flux:tab.group>
    @endif
</div>
