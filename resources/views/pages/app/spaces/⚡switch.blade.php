<?php

use App\Models\Space;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

new class extends Component {
    public Space $targetSpace;
    public Space $currentSpace;
    public string $intendedUrl;

    public function mount(): void
    {
        $targetId = request()->query('target_space_id');
        $this->intendedUrl = $this->sanitizeIntendedUrl(request()->query('intended_url'));

        $this->targetSpace = Space::findOrFail($targetId);

        if (auth()->user()->cannot('view', $this->targetSpace)) {
            abort(403);
        }

        $this->currentSpace = Space::current() ?? auth()->user()->spaces()->first();
    }

    public function confirm(): void
    {
        session(['current_space_id' => $this->targetSpace->id]);

        $this->redirect($this->intendedUrl, navigate: true);
    }

    private function sanitizeIntendedUrl(?string $url): string
    {
        $fallback = route('root');

        if (! is_string($url) || $url === '') {
            return $fallback;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return $fallback;
        }

        if (isset($parts['host']) && $parts['host'] !== request()->getHost()) {
            return $fallback;
        }

        $path = $parts['path'] ?? '/';

        if (! str_starts_with($path, '/')) {
            return $fallback;
        }

        return $path.(isset($parts['query']) ? '?'.$parts['query'] : '').(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    public function cancel(): void
    {
        $this->redirect(route('root'), navigate: true);
    }
};
?>

<div class="flex flex-col grow">
    <x-container class="p-6 lg:p-8 flex flex-col gap-6 lg:gap-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Confirm Switch</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-app.section-header :title="__('Switch Space?')"
                              :subtitle="__('The resource you are trying to view belongs to a different space. Do you want to switch to the space :spaceName?', ['spaceName' => $this->targetSpace->name])">
        </x-app.section-header>

        <div class="flex items-center gap-4">
            <flux:button wire:click="cancel" variant="outline">
                Don't switch Space
            </flux:button>
            <flux:button wire:click="confirm" icon:trailing="arrow-right" variant="primary">
                Switch Space
            </flux:button>
        </div>
    </x-container>
</div>
