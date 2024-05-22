@props(['text' => 'Tooltip'])
<div class="relative group cursor-default z-10">
    <span
        class="absolute flex flex-col left-1/2 transition-all select-none -translate-x-1/2 items-center opacity-0 bottom-[110%] scale-0 group-hover:opacity-100 group-hover:scale-100">
        <span class="bg-gray-800 px-2 py-0.5 rounded">{{ $text }}</span>
        <span
            class="w-0 h-0 border-l-8 border-l-transparent border-r-8 border-r-transparent border-t-8 border-gray-800">
        </span>
    </span>
    {{ $slot }}
</div>
