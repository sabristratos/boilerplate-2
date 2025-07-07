@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="squares-2x2" class="size-4" />
        Display Options
    </flux:heading>
    <flux:select 
        wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.variant" 
        label="Variant"
    >
        <flux:select.option value="default">Default</flux:select.option>
        <flux:select.option value="cards">Cards</flux:select.option>
    </flux:select>
</div> 
