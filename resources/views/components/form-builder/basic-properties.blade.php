@props(['selectedElement', 'selectedElementIndex'])
<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.label" label="Label" />
<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.placeholder" label="Placeholder" />
@if($selectedElement['type'] === 'select')
    <flux:textarea wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.options" label="Options" help="One option per line." />
@endif
@if($selectedElement['type'] === 'textarea')
    <flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.rows" type="number" label="Rows" placeholder="3" help="Number of visible text lines" />
@endif 
