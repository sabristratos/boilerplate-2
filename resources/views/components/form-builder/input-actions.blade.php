@props(['selectedElement', 'selectedElementIndex'])
<flux:heading size="md" class="flex items-center gap-2">
    <flux:icon name="cursor-arrow-rays" class="size-4" />
    Input Actions
</flux:heading>
<div class="space-y-3">
    @if($selectedElement['type'] !== \App\Enums\FormElementType::TEXTAREA->value)
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.clearable" />
            <flux:label>Clearable</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.copyable" />
            <flux:label>Copyable</flux:label>
        </flux:field>
    @endif
    @if($selectedElement['type'] === 'password')
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.viewable" />
            <flux:label>Show/Hide Toggle</flux:label>
        </flux:field>
    @endif
</div> 
