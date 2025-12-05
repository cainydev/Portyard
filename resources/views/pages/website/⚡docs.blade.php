<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::website')] class extends Component {
    //
};
?>

<div>
    <div class="max-w-4xl">
        <flux:badge icon="pencil">Work in progress</flux:badge>

        <div class="mt-10 lg:mt-12">
            <flux:heading level="1"
                          class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl! max-w-4xl">
                Comprehensive Docs are coming soon
            </flux:heading>

            <flux:subheading class="mt-8 text-xl max-w-2xl">
                We're actively building out a full documentation hub for Portyard and Dockhand. For now,
                the most detailed and up-to-date information, including installation guides and API usage,
                can be found directly in the project repositories.
            </flux:subheading>
        </div>

        <div class="mt-10 lg:mt-12 flex flex-col sm:flex-row items-start gap-6">
            <flux:button href="https://github.com/cainydev/Portyard#readme" variant="primary"
                         icon-trailing="arrow-right">
                Portyard on Github
            </flux:button>

            <flux:button href="https://github.com/cainydev/laravel-dockhand#readme" variant="outline"
                         icon-trailing="book-open">
                Dockhand on Github
            </flux:button>
        </div>
    </div>
</div>
