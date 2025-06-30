<x-layouts.app>
    <x-slot:title>
        {{ __('navigation.analytics') }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <flux:heading size="xl">{{ __('navigation.analytics') }}</flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    View detailed analytics and insights about your website performance.
                </flux:text>
            </div>

            <flux:card>
                <flux:callout icon="information-circle" variant="secondary">
                    <flux:callout.heading>Analytics Coming Soon</flux:callout.heading>
                    <flux:callout.text>
                        This feature is currently under development. You'll be able to view detailed analytics, 
                        visitor statistics, and performance metrics here.
                    </flux:callout.text>
                </flux:callout>
            </flux:card>
        </div>
    </div>
</x-layouts.app> 