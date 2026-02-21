@props(['href' => null])

<flux:brand {{ $attributes }}
            class="font-semibold tracking-wide"
            wire:navigate
            :href="$href ?? route('root')"
            :logo="Vite::asset('resources/images/portyard.png')"
            :name="config('app.name')"
            :alt="config('app.name') . ' Logo'"
/>
