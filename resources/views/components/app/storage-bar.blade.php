@props(['space'])

@php
    $percent = $space->storageUsedPercent();
    $usedGb = round($space->storage_used_bytes / (1024 * 1024 * 1024), 2);
    $limitGb = round(\App\Models\Space::BETA_STORAGE_LIMIT_BYTES / (1024 * 1024 * 1024));

    $color = match (true) {
        $percent >= 90 => 'red',
        $percent >= 75 => 'amber',
        default => 'zinc',
    };

    $textColor = match (true) {
        $percent >= 90 => 'text-red-600 dark:text-red-400',
        $percent >= 75 => 'text-amber-600 dark:text-amber-400',
        default => '',
    };
@endphp

<div {{ $attributes->class('space-y-1') }}>
    <div class="flex items-center justify-between">
        <flux:text variant="subtle" size="sm">{{ __('Storage') }}</flux:text>
        <flux:text size="sm" class="{{ $textColor }}">
            {{ $usedGb }} GB / {{ $limitGb }} GB {{ __('used') }}
        </flux:text>
    </div>

    <flux:progress :$color :value="$percent" />

    @if ($percent >= 90)
        <flux:text size="sm" class="{{ $textColor }}">
            {{ $percent >= 100 ? __('Storage quota exceeded. Pushes are disabled.') : __('Storage is almost full.') }}
        </flux:text>
    @endif
</div>
