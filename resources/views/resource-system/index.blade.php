<x-layouts.app>
    <x-slot:title>
        {{ $resource::pluralLabel() }}
    </x-slot:title>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <flux:heading size="xl">{{ $resource::pluralLabel() }}</flux:heading>
                <flux:button
                    href="{{ route('admin.resources.' . $resource::uriKey() . '.create') }}"
                    variant="primary"
                    icon="plus"
                >
                    {{ __('buttons.create_item', ['item' => $resource::singularLabel()]) }}
                </flux:button>
            </div>

            @livewire('resource-system.resource-table', ['resource' => $resource])
        </div>
    </div>
</x-layouts.app>
