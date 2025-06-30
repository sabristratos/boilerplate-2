<x-layouts.app>
    <x-slot:title>
        {{ $resource::pluralLabel() }}
    </x-slot:title>

    @livewire('resource-system.resource-table', ['resource' => $resource])
</x-layouts.app>
