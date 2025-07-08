<x-layouts.app>
    <x-slot:title>
        {{ __('buttons.create_item', ['item' => $resource::singularLabel()]) }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <flux:heading size="xl">
                    {{ __('buttons.create_item', ['item' => $resource::singularLabel()]) }}
                </flux:heading>
                <flux:button
                    href="{{ route('admin.resources.' . $resource::uriKey() . '.index') }}"
                    variant="outline"
                    icon="arrow-left"
                >
                    {{ __('buttons.back_to_items', ['items' => $resource::pluralLabel()]) }}
                </flux:button>
            </div>

            @livewire('resource-system.resource-form', ['resourceClass' => get_class($resource)])
        </div>
    </div>
</x-layouts.app>
