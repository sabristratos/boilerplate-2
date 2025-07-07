@props(['selectedElement', 'selectedElementIndex', 'selectedElementOptionsArray'])

<flux:input 
    wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.label" 
    label="Label" 
    placeholder="Enter field label"
/>
<flux:input 
    wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.placeholder" 
    label="Placeholder" 
    placeholder="Enter placeholder text"
/>

@if(in_array($selectedElement['type'], ['select', 'checkbox', 'radio']))
    <livewire:form-builder-options-repeater 
        :element-index="$selectedElementIndex" 
        property-path="options"
        :options="$selectedElementOptionsArray"
    />
@endif

@if($selectedElement['type'] === 'textarea')
    <flux:input 
        wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.rows" 
        type="number" 
        label="Rows" 
        placeholder="3" 
        help="Number of visible text lines" 
    />
@endif 

@if($selectedElement['type'] === \App\Enums\FormElementType::SubmitButton->value)
    <div class="mb-4">
        <flux:input
            wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.buttonText"
            :label="__('forms.field_types.submit_button.name')"
        />
    </div>
@endif 
