<x-layouts.app>
    <x-slot:title>
        {{ __('navigation.help') }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <flux:heading size="xl">{{ __('navigation.help') }}</flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    Get help and documentation for using the admin panel.
                </flux:text>
            </div>

            <flux:card>
                <flux:callout icon="information-circle" variant="secondary">
                    <flux:callout.heading>Help & Documentation Coming Soon</flux:callout.heading>
                    <flux:callout.text>
                        This feature is currently under development. You'll find comprehensive documentation, 
                        tutorials, and support resources here to help you make the most of the admin panel.
                    </flux:callout.text>
                </flux:callout>
            </flux:card>
        </div>
    </div>
</x-layouts.app> 