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
    <x-container class="p-6 lg:p-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:icon.sparkles class="h-7 w-7 fill-current"/>
            <flux:heading size="xl"><span x-text="greeting">
                {{ __('Good afternoon') }}</span>, {{ auth()->user()->name }}!
            </flux:heading>
        </div>

        <div class="flex items-center gap-4">
            <flux:link variant="subtle" target="_blank"
                       href="https://github.com/cainydev/Portyard/issues">
                Give Feedback
            </flux:link>
        </div>
    </x-container>

    <x-container class="p-6 lg:p-8 flex flex-col gap-4">
        <flux:heading size="lg">Latest pushes</flux:heading>
        <flux:text>No recent pushes.</flux:text>
    </x-container>
</div>
