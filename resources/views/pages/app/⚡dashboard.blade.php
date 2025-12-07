<?php

use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'greetings' => [
                'morning' => __('Good morning'),
                'afternoon' => __('Good afternoon'),
                'evening' => __('Good evening'),
                'night' => __('Good night'),
            ],
        ];
    }
};
?>

<div x-data="{
    greeting: '',
    greetings: @js($greetings),
    init() {
        const hour = new Date().getHours();

        if (hour >= 5 && hour < 12) {
            this.greeting = this.greetings.morning;
        } else if (hour >= 12 && hour < 18) {
            this.greeting = this.greetings.afternoon;
        } else if (hour >= 18 && hour < 24) {
            this.greeting = this.greetings.evening;
        } else {
            this.greeting = this.greetings.night;
        }
    }
}" class="flex flex-col grow">
    <x-container class="p-6 lg:p-8 flex flex-col gap-6 lg:gap-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Dashboard</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-app.section-header :subtitle="__('Overview of your repositories and recent activity.')">
            <x-slot:title>
                <span x-text="greeting">{{ __('Good afternoon') }}</span>, {{ auth()->user()->name }}!
            </x-slot:title>

            <flux:link variant="subtle" target="_blank"
                       href="https://github.com/cainydev/Portyard/issues">
                Give Feedback
            </flux:link>
        </x-app.section-header>
    </x-container>

    <x-container class="p-6 lg:p-8 flex flex-col gap-4">
        <flux:heading size="lg">Latest pushes</flux:heading>
        <flux:text>No recent pushes.</flux:text>
    </x-container>
</div>
