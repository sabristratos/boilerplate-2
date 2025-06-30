<x-layouts.app>
    <x-slot:title>
        {{ __('navigation.reports') }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <flux:heading size="xl">{{ __('navigation.reports') }}</flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    Generate and view detailed reports about your content and user activity.
                </flux:text>
            </div>

            <flux:card>
                <flux:callout icon="information-circle" variant="secondary">
                    <flux:callout.heading>Reports Coming Soon</flux:callout.heading>
                    <flux:callout.text>
                        This feature is currently under development. You'll be able to generate custom reports, 
                        export data, and view detailed insights about your content and users.
                    </flux:callout.text>
                </flux:callout>
            </flux:card>
        </div>
    </div>
</x-layouts.app> 