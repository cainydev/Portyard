<x-filament-panels::page>
        @isset($this->table) {{ $this->table }} @endisset
    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
