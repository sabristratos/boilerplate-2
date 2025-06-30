@props(['element', 'properties', 'fluxProps'])

<flux:field variant="inline">
    <flux:radio 
        variant="{{ $fluxProps['variant'] ?? 'default' }}"
    />
    <flux:label>{{ $properties['label'] }}</flux:label>
</flux:field>
