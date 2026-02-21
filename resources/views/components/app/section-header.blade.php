@props([
    'title' => '',
    'subtitle' => null,
    'actions' => null,
])

<div {{ $attributes->class("flex gap-8 items-start flex-col md:flex-row md:justify-between") }}>
    <div class="space-y-1">
        <flux:heading size="xl">
            {{ $title }}
        </flux:heading>

        @isset($subtitle)
            <flux:subheading size="xl">
                {{ $subtitle }}
            </flux:subheading>
        @endisset
    </div>

    @if($slot->isNotEmpty())
        <div class="flex items-center gap-4">
            {{ $slot }}
        </div>
    @endif
</div>
