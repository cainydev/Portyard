@props(['title' => null, 'subtitle' => null, 'actions' => null])

<div {{ $attributes->class('flex flex-col gap-6 lg:gap-8 grow p-6 lg:p-8 not-last:border-b border-stitched') }}>
    @if($title)
        <div class="flex items-center justify-between gap-6 lg:gap-8">
        <span>
            <flux:heading size="lg" class="mb-0!">{{ $title }}</flux:heading>
            @if($subtitle)
                <flux:subheading class="max-w-3xl mb-0!">{{ $subtitle }}</flux:subheading>
            @endif
        </span>

            @if($actions)
                <div class="flex items-center gap-4 lg:gap-6">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
