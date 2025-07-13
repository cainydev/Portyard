@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'repository',
    'actions' => []
])

<div {{ $attributes->class(['fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:flex-row sm:items-center border-b border-gray-300 dark:border-gray-800']) }}>
    <div class="flex flex-col w-full gap-y-4">
        <div class="grow">
            <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Webhooks
            </h3>

            <div class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400 space-y-3">
                <p>Webhooks are HTTP callbacks that are triggered by certain events. When one of the selected events is triggered, the webhook will receive a POST request with the full event payload. This is useful for continuous integration workflows.</p>
                <p>Currently, we support the following events:</p>
                <ul class="space-y-1">
                    <li><strong>Tag pushed</strong>: When a new tag is pushed to the repository</li>
                    <li><strong>Tag updated</strong>: When an existing tag is updated in the repository</li>
                </ul>
            </div>
        </div>

        @if ($actions)
            <x-filament-tables::actions
                :actions="$actions"
                :alignment="Alignment::Start"
                :wrap="true"
                class=""/>
        @endif
    </div>
</div>
