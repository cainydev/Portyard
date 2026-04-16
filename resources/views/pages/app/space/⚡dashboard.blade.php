<?php

use App\Models\Space;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component {
    public Space $space;

    public function with(): array
    {
        return [
            "greetings" => [
                "morning" => __("Good morning"),
                "afternoon" => __("Good afternoon"),
                "evening" => __("Good evening"),
                "night" => __("Good night"),
            ],
        ];
    }

    public function render()
    {
        return $this->view()->title($this->space->name);
    }
};
?>

<div
    x-data="{
        greeting: '',
        greetings: @js($greetings),
        init() {
            const hour = new Date().getHours()

            if (hour >= 5 && hour < 12) {
                this.greeting = this.greetings.morning
            } else if (hour >= 12 && hour < 18) {
                this.greeting = this.greetings.afternoon
            } else if (hour >= 18 && hour < 24) {
                this.greeting = this.greetings.evening
            } else {
                this.greeting = this.greetings.night
            }
        },
    }"
    class="flex flex-col grow">
    <x-container class="p-6 lg:p-8 flex flex-col gap-6 lg:gap-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ $space->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Dashboard</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-app.section-header :subtitle="__('Overview of the recent pushes and other activities in this Space.')">
            <x-slot:title>
                <span x-text="greeting">{{ __("Good afternoon") }}</span>
                , {{ auth()->user()->name }}!
            </x-slot>

            <flux:link variant="subtle" target="_blank" href="https://github.com/cainydev/Portyard/issues">
                Give Feedback
            </flux:link>
        </x-app.section-header>
    </x-container>

    <x-container class="px-6 lg:px-8">
        <x-app.storage-bar :space="$space" />
    </x-container>

    <x-container class="p-6 lg:p-8 flex flex-col gap-4">
        <flux:heading size="lg">Recent activity</flux:heading>
        <livewire:app.activity-log
            :space="$space"
            :actions="[
                                   \App\Enums\Action::SpaceCreated,
                                   \App\Enums\Action::RepositoryCreated,
                                   \App\Enums\Action::RepositoryDeleted,
                                   \App\Enums\Action::RepositoryTransferred,
                                   \App\Enums\Action::ManifestPushed,
                                   \App\Enums\Action::ManifestDeleted,
                                   \App\Enums\Action::TagPushed,
                                   \App\Enums\Action::TagDeleted,
                                   \App\Enums\Action::MemberAccepted,
                               ]" />
    </x-container>
</div>
