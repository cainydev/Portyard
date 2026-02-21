<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Your Profile')] class extends Component {
    //
};
?>

<div class="flex flex-col grow">
    <x-container>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ auth()->user()->currentSpace()->name }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Settings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Profile</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </x-container>
</div>
