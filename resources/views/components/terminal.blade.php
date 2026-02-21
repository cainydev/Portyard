<div
    class="rounded-xl overflow-hidden shadow">
    <div class="flex items-center gap-2 px-4 py-3 bg-zinc-700 border-b border-neutral-800">
        <div class="size-3 rounded-full bg-red-500"></div>
        <div class="size-3 rounded-full bg-yellow-500"></div>
        <div class="size-3 rounded-full bg-green-500"></div>
    </div>
    <div class="p-6 text-gray-300 bg-neutral-900 font-mono flex flex-col gap-2">
        {{ $slot }}
    </div>
</div>
