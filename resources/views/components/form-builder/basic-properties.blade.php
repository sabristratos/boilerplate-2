@props(['selectedElement', 'selectedElementIndex'])
<<<<<<< HEAD
<flux:input wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.label" label="Label" />
<flux:input wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.placeholder" label="Placeholder" />

@if(in_array($selectedElement['type'], ['select', 'checkbox', 'radio']))
    @php
        $optionsArray = $this->getElementOptionsArray($selectedElementIndex);
    @endphp
    <livewire:form-builder-options-repeater 
        :element-index="$selectedElementIndex" 
        property-path="options"
        :options="$optionsArray"
    />
=======
<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.label" label="Label" />
<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.placeholder" label="Placeholder" />
@if($selectedElement['type'] === 'select')
    <flux:textarea wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.options" label="Options" help="One option per line." />
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
@endif

@if($selectedElement['type'] === 'textarea')
    <flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.rows" type="number" label="Rows" placeholder="3" help="Number of visible text lines" />
@endif 
