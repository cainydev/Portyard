<x-filament-panels::page
    @class([
        'fi-resource-edit-repository-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $repository->path,
    ])
>

        @isset($this->form) {{ $this->form }} @endisset
        @isset($this->table) {{ $this->table }} @endisset
    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
