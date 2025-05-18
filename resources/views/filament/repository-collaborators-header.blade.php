@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'repository',
    'actions' => []
])

<div
    {{
        $attributes->class([
            'fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:flex-row sm:items-center',
        ])
    }}
>
    <div class="flex flex-col w-full gap-y-4">
        <div class="grow">
            <h3
                class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white"
            >
                Collaborators
            </h3>

            <div class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400 space-y-3">
                <p>You can assign different roles to your collaborators. Here is a quick overview.</p>

                <ul class="space-y-1">
                    @if($repository->public)
                        <li><strong>Anyone</strong>: Can <em>view</em> and <em>pull</em> this repository.</li>
                    @else
                        <li><strong>Viewers</strong>: Can <em>view</em> and <em>pull</em> a private repository.</li>
                    @endif
                    <li><strong>Developers</strong>: Can additionally <em>push</em> to the repository.</li>
                    <li><strong>Maintainers</strong>: Can additionally <em>edit general settings</em>
                        and <em>edit webhook settings</em>.
                    </li>
                    <li><strong>Owners</strong>: Can additionally <em>edit permissions</em>, <em>change visibility</em>,
                        and
                        <em>delete</em> the repository.
                    </li>
                </ul>
            </div>
        </div>

        @if ($actions)
            <x-filament-tables::actions
                :actions="$actions"
                :alignment="Alignment::Start"
                wrap
                class=""
            />
        @endif
    </div>
