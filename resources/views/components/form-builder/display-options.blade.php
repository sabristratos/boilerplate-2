@props(['selectedElement', 'selectedElementIndex'])
<flux:heading size="md" class="flex items-center gap-2">
    <flux:icon name="eye" class="size-4" />
    Display Options
</flux:heading>
<div class="space-y-4">
    <flux:select 
        wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.variant" 
        label="Variant"
    >
        <flux:select.option value="default">Default</flux:select.option>
        <flux:select.option value="cards">Cards</flux:select.option>
    </flux:select>
</div> 
