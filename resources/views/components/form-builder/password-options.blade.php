@props(['selectedElement', 'selectedElementIndex'])
<flux:heading size="md" class="flex items-center gap-2">
    <flux:icon name="lock-closed" class="size-4" />
    Password Options
</flux:heading>
<div class="space-y-3">
    <flux:field variant="inline">
        <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.viewable" />
        <flux:label>Show/Hide Toggle</flux:label>
    </flux:field>
    <flux:field variant="inline">
        <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.clearable" />
        <flux:label>Clearable</flux:label>
    </flux:field>
    <flux:field variant="inline">
        <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.copyable" />
        <flux:label>Copyable</flux:label>
    </flux:field>
</div> 
