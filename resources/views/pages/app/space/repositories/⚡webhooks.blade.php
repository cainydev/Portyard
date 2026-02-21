<?php

use App\Models\Repository;
use Livewire\Component;

new class extends Component {
    public Repository $repository;

    public function mount(Repository $repository): void
    {
        $this->repository = $repository;
    }

    public function render()
    {
        return $this->view()
            ->title(__('Webhooks') . " · {$this->repository->path}");
    }
};
?>

<x-layouts.repository :repository="$repository">
    <x-container inset>
        <x-app.settings.section :title="__('Coming Soon')"
                                :subtitle="__('Webhooks will be available in a future update. Stay tuned!')">
        </x-app.settings.section>
    </x-container>
</x-layouts.repository>
