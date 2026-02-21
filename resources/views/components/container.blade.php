@props([
    'as' => null,
    'section' => true,
    'sticky' => false,
    'inset' => false,
    'left' => null,
    'right' => null,
    'border' => null,
    'borderTop' => null,
    'borderBottom' => 'full',
])

@php
    // 1. Resolve Tag
    $tag = $as ?? ($section ? 'section' : 'div');

    // 2. Normalize Border Logic
    $normalize = fn($v) => $v === true ? 'full' : $v;
    $top = $normalize($border ?? $borderTop);
    $bottom = $normalize($border ?? $borderBottom);

    // 3. Outer Classes (Viewport Wrapper)
    $outerClasses = Flux::classes('bg-white dark:bg-zinc-800 w-full')
        ->add('@container grid sm:grid-cols-[minmax(0,1fr)_minmax(0,var(--container-7xl))_minmax(0,1fr)] last-of-type:grow')
        ->add($sticky ? match (boolval($top)) {
            true => 'sticky top-[calc(var(--header-height,0px)-1px)]',
            false => 'sticky top-[var(--header-height,0px)]',
        } : null)
        ->add(match ($top) {
            'full' => 'border-t border-stitched',
            default => null,
        })
        ->add(match ($bottom) {
            'full' => 'border-b border-stitched',
            default => null,
        });

    // 4. INNER Classes (Content Container)
    $innerClasses = Flux::classes('col-start-1 sm:col-start-2 overflow-hidden')
        ->add('border-stitched @min-7xl:border-x')
        ->add(match ($top) {
            'center' => 'border-t',
            default => null,
        })
        ->add(match ($bottom) {
            'center' => 'border-b',
            default => null,
        })
        ->add($inset ? null : '[:where(&)]:p-6 lg:[:where(&)]:p-8');
@endphp

<{{ $tag }} class="{{ $outerClasses }}">

{{-- Left Gutter --}}
<div class="hidden sm:block col-start-1 min-w-0 overflow-hidden">
    @isset($left)
        <div class="flex flex-col gap-6 p-6 lg:p-8">
            {{ $left }}
        </div>
    @endisset
</div>

{{-- Center Content (Receives $attributes) --}}
<div {{ $attributes->class($innerClasses) }}>
    {{ $slot }}
</div>

{{-- Right Gutter --}}
<div class="hidden sm:block col-start-3 min-w-0 overflow-hidden">
    @isset($right)
        <div class="flex flex-col gap-6 p-6 lg:p-8">
            {{ $right }}
        </div>
    @endisset
</div>

</{{ $tag }}>
