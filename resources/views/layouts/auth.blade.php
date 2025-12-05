<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <title>{{ $title ?? config('app.name') }}</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:main :x-data="$alpine ?? '{}'"
               class="grid min-h-screen p-0! w-full grid-cols-1 sm:grid-cols-[1fr_1rem_min(var(--container-lg),100dvw)_1rem_1fr] grid-rows-[1fr_auto_1fr]">

        <div
            class="pointer-events-none hidden sm:block col-start-2 bg-diag-lines row-span-full border-x border-dashed border-zinc-300 dark:border-zinc-600"></div>

        <div
            class="pointer-events-none hidden sm:block col-start-4 bg-diag-lines row-span-full border-x border-dashed border-zinc-300 dark:border-zinc-600"></div>

        <div
            class="pointer-events-none col-span-full col-start-1 row-start-2 border-y border-dashed border-zinc-300 dark:border-zinc-600"></div>

        @isset($header)
            <div class="col-start-1 sm:col-start-3 row-start-1 p-6 lg:p-8 flex flex-col">
                {{ $header }}
            </div>
        @endisset

        <div class="col-start-1 sm:col-start-3 row-start-2 p-6 lg:p-8">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="col-start-1 sm:col-start-3 row-start-3 p-6 lg:p-8 flex flex-col">
                {{ $footer }}
            </div>
        @endisset
    </flux:main>

    @fluxScripts
</body>
</html>
