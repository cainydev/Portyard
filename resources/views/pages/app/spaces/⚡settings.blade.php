<?php

use Livewire\Component;

new class extends Component {
    //
};
?>


<div class="flex flex-col grow">
    <x-container class="p-6 lg:p-8 flex flex-col gap-6 lg:gap-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Settings</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </x-container>
</div>
