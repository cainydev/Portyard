@php
    if (! isset($scrollTo)) {
        $scrollTo = "body";
    }

    $scrollIntoViewJsSnippet =
        $scrollTo !== false
            ? <<<JS
               (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
            JS
            : "";

    $mobileClasses = fn ($disabled = false) => Flux::classes()
        ->add("relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 transition ease-in-out duration-150")
        ->add("border border-stitched bg-transparent")
        ->add(
            match (true) {
                $disabled => "text-zinc-500 cursor-default opacity-75 dark:text-zinc-400",
                default => "text-zinc-800 hover:bg-zinc-50 focus:outline-none dark:text-white dark:hover:bg-zinc-600/75",
            },
        );

    $desktopClasses = fn ($type = "page", $active = false, $disabled = false, $isFirst = false, $isLast = false) => Flux::classes()
        ->add("relative inline-flex items-center text-sm font-medium leading-5 transition ease-in-out duration-150")
        ->add(
            match ($type) {
                "icon" => "px-3 py-3",
                default => "px-5 py-3",
            },
        )
        ->add("border-stitched")
        ->add($isLast ? "border-x" : "border-s")
        ->add(
            match (true) {
                $active => "text-[var(--color-accent-foreground)] bg-zinc-800/5 cursor-default dark:bg-white/10 dark:text-white",
                $disabled => "text-zinc-500 cursor-default opacity-75 dark:text-zinc-500",
                default => "text-zinc-800 hover:bg-zinc-50 focus:z-10 focus-visible:bg-zinc-50 dark:focus-visible:bg-zinc-600/75 focus:outline-none dark:text-white dark:hover:bg-zinc-600/75",
            },
        );
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Mobile Pagination --}}
            <div class="flex justify-between flex-1 sm:hidden gap-3">
                @if ($paginator->onFirstPage())
                    <span class="{{ $mobileClasses(disabled: true) }}">
                        {!! __("pagination.previous") !!}
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        dusk="previousPage{{ $paginator->getPageName() == "page" ? "" : "." . $paginator->getPageName() }}.before"
                        class="{{ $mobileClasses() }}">
                        {!! __("pagination.previous") !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        dusk="nextPage{{ $paginator->getPageName() == "page" ? "" : "." . $paginator->getPageName() }}.before"
                        class="{{ $mobileClasses() }}">
                        {!! __("pagination.next") !!}
                    </button>
                @else
                    <span class="{{ $mobileClasses(disabled: true) }}">
                        {!! __("pagination.next") !!}
                    </span>
                @endif
            </div>

            {{-- Desktop Pagination --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-stretch sm:justify-between">
                <div class="flex items-center pe-6 lg:pe-8">
                    <p class="text-sm text-zinc-700 leading-5 dark:text-zinc-400">
                        <span>{!! __("Showing") !!}</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>{!! __("to") !!}</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>{!! __("of") !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! __("results") !!}</span>
                    </p>
                </div>

                <span class="border-l border-stitched bg-diag-lines grow"></span>

                <div>
                    <span class="relative z-0 inline-flex rtl:flex-row-reverse">
                        @if ($paginator->onFirstPage())
                            <span
                                aria-disabled="true"
                                aria-label="{{ __("pagination.previous") }}"
                                class="{{ $desktopClasses(type: "icon", disabled: true, isFirst: true) }}"
                                aria-hidden="true">
                                <flux:icon.chevron-left class="size-5" />
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                dusk="previousPage{{ $paginator->getPageName() == "page" ? "" : "." . $paginator->getPageName() }}.after"
                                class="{{ $desktopClasses(type: "icon", isFirst: true) }}"
                                aria-label="{{ __("pagination.previous") }}">
                                <flux:icon.chevron-left class="size-5" />
                            </button>
                        @endif

                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true" class="{{ $desktopClasses(disabled: true) }}">
                                    {{ $element }}
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span
                                            wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}"
                                            aria-current="page"
                                            class="{{ $desktopClasses(active: true) }}">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button
                                            wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}"
                                            type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                            class="{{ $desktopClasses() }}"
                                            aria-label="{{ __("Go to page :page", ["page" => $page]) }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        @if ($paginator->hasMorePages())
                            <button
                                type="button"
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                dusk="nextPage{{ $paginator->getPageName() == "page" ? "" : "." . $paginator->getPageName() }}.after"
                                class="{{ $desktopClasses(type: "icon", isLast: true) }}"
                                aria-label="{{ __("pagination.next") }}">
                                <flux:icon.chevron-right class="size-5" />
                            </button>
                        @else
                            <span
                                aria-disabled="true"
                                aria-label="{{ __("pagination.next") }}"
                                class="{{ $desktopClasses(type: "icon", disabled: true, isLast: true) }}"
                                aria-hidden="true">
                                <flux:icon.chevron-right class="size-5" />
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
