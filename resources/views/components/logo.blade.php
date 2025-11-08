@props(['href' => route('web.home')])

<a href="{{ $href }}" class="p-4 dark:bg-white rounded-lg">
    <img src="{{ Vite::asset('resources/images/portyard.png') }}" alt="Portyard Logo" class="h-8 w-auto"/>
</a>
