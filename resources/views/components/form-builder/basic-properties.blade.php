@props(['selectedElement', 'selectedElementIndex'])

<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.label" label="Label" />
<flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.placeholder" label="Placeholder" />

@if(in_array($selectedElement['type'], ['select', 'checkbox', 'radio']))
    @php
        $optionsArray = $this->getElementOptionsArray($selectedElementIndex);
    @endphp
    <livewire:form-builder-options-repeater 
        :element-index="$selectedElementIndex" 
        property-path="options"
        :options="$optionsArray"
    />
@endif

@if($selectedElement['type'] === 'textarea')
    <flux:input wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.rows" type="number" label="Rows" placeholder="3" help="Number of visible text lines" />
@endif 
