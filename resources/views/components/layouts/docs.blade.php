<div class="flex flex-col grow">
    <x-container>
        <flux:badge icon="book-open">Documentation</flux:badge>

        <x-app.section-header
            :title="__('Documentation')"
            :subtitle="__('Learn how to use Portyard to store and manage your container images.')"
            class="mt-4"
        />
    </x-container>

    <x-container inset class="flex flex-col grow">
        <div class="grid grid-cols-1 md:grid-cols-[16rem_1fr] grow">
            <nav class="flex flex-col border-b md:border-b-0 md:border-r border-stitched">
                <div class="p-4">
                    <flux:navlist>
                        <flux:navlist.item
                            :href="route('website.docs.overview')"
                            :current="request()->routeIs('website.docs.overview')"
                            icon="rocket-launch"
                            wire:navigate>
                            Getting Started
                        </flux:navlist.item>
                        <flux:navlist.item
                            :href="route('website.docs.authentication')"
                            :current="request()->routeIs('website.docs.authentication')"
                            icon="key"
                            wire:navigate>
                            Authentication
                        </flux:navlist.item>
                        <flux:navlist.item
                            :href="route('website.docs.github-actions')"
                            :current="request()->routeIs('website.docs.github-actions')"
                            icon="code-bracket"
                            wire:navigate>
                            GitHub Actions
                        </flux:navlist.item>
                        <flux:navlist.item
                            :href="route('website.docs.docker-cli')"
                            :current="request()->routeIs('website.docs.docker-cli')"
                            icon="command-line"
                            wire:navigate>
                            Docker CLI
                        </flux:navlist.item>
                    </flux:navlist>
                </div>
                <div class="border-t border-stitched bg-diag-lines grow hidden md:block"></div>
            </nav>

            <div class="p-6 lg:p-8">
                {{ $slot }}
            </div>
        </div>
    </x-container>
</div>
