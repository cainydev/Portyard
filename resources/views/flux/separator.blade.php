@blaze

@props([
    "orientation" => null,
    "vertical" => false,
    "variant" => null,
    "faint" => false,
    "text" => null,
])

@php
    $orientation ??= $vertical ? "vertical" : "horizontal";

    $classes = Flux::classes("[print-color-adjust:exact] border-stitched")
        // 1. Map variants to border-colors instead of bg-colors
        ->add(
            match ($variant) {
                "subtle" => "border-zinc-800/5 dark:border-white/10",
                default => "border-zinc-800/15 dark:border-white/20",
            },
        )
        // 2. Define geometry: Use 0px dimension + border side instead of 1px dimension + bg
        ->add(
            match ($orientation) {
                "horizontal" => "h-0 border-t w-full",
                "vertical" => "self-stretch self-center h-auto w-0 border-l",
            },
        );
@endphp

<?php if ($text): ?>

<div
    data-orientation="{{ $orientation }}"
    class="flex items-center w-full"
    role="none"
    data-flux-separator
>
    <div {{ $attributes->class([$classes, "grow"]) }}></div>

    <div {{ $attributes->class([$classes, "grow"]) }}></div>
</div>

<?php else: ?>

<div
    data-orientation="{{ $orientation }}"
    role="none"
    {{ $attributes->class($classes, "shrink-0") }}
    data-flux-separator
></div>

<?php endif; ?>
