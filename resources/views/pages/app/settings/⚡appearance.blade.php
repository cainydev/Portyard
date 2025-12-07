<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Settings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Appearance</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </x-container>
</div>
