@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="list-bullet" class="size-4" />
        Select Options
    </flux:heading>
    <div class="space-y-3">
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.clearable" />
            <flux:label>Clearable</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.searchable" />
            <flux:label>Searchable</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.multiple" />
            <flux:label>Multiple Selection</flux:label>
        </flux:field>
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.variant" 
            label="Variant"
        >
            <flux:select.option value="default">Default</flux:select.option>
            <flux:select.option value="listbox">Listbox</flux:select.option>
            <flux:select.option value="combobox">Combobox</flux:select.option>
        </flux:select>
    </div>
</div> 
