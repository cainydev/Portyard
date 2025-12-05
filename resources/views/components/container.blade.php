@props([
    'left' => null,
    'right' => null,
    'section' => true,
    'divide' => true,
])

@if($section)
    <section
        @class(["@container grid sm:grid-cols-[minmax(0,1fr)_minmax(0,var(--container-7xl))_minmax(0,1fr)] last-of-type:grow w-full", "border-b border-stitched" => $divide])>

        <div class="hidden sm:block col-start-1 min-w-0 overflow-hidden">
            @isset($left)
                <div class="flex flex-col gap-6 p-6 lg:p-8">
                    {{ $left }}
                </div>
            @endisset
        </div>

        <div {{ $attributes->class(["col-start-1 sm:col-start-2 @min-7xl:border-x border-stitched"]) }}>
            {{ $slot }}
        </div>

        <div class="hidden sm:block col-start-3 min-w-0 overflow-hidden">
            @isset($right)
                <div class="flex flex-col gap-6 p-6 lg:p-8">
                    {{ $right }}
                </div>
            @endisset
        </div>
    </section>
@else
    <div
        @class(["@container grid sm:grid-cols-[minmax(0,1fr)_minmax(0,var(--container-7xl))_minmax(0,1fr)] last-of-type:grow w-full", "border-b border-stitched" => $divide])>
        <div class="hidden sm:block col-start-1 min-w-0 overflow-hidden">
            @isset($left)
                <div class="flex flex-col gap-6 p-6 lg:p-8">
                    {{ $left }}
                </div>
            @endisset
        </div>

        <div {{ $attributes->class(["col-start-1 sm:col-start-2 @min-7xl:border-x border-stitched"]) }}>
            {{ $slot }}
        </div>

        <div class="hidden sm:block col-start-3 min-w-0 overflow-hidden">
            @isset($right)
                <div class="flex flex-col gap-6 p-6 lg:p-8">
                    {{ $right }}
                </div>
            @endisset
        </div>
    </div>
@endif
