@props(['selectedElement', 'selectedElementIndex', 'availableValidationRules', 'availableIcons'])
<!-- Input Actions Section -->
@if(in_array($selectedElement['type'], ['text', 'textarea', 'email', 'number', 'password', 'file']))
    <x-form-builder.input-actions 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Icons Section -->
@if(in_array($selectedElement['type'], ['text', 'textarea', 'email', 'number', 'password', 'file']))
    <x-form-builder.icon-settings 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
        :availableIcons="$availableIcons"
    />
@endif

<!-- Select Options Section -->
@if($selectedElement['type'] === 'select')
    <x-form-builder.select-options 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Display Options Section -->
@if(in_array($selectedElement['type'], ['checkbox', 'radio']))
    <x-form-builder.display-options 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Date Picker Configuration Section -->
@if($selectedElement['type'] === 'date')
    <x-form-builder.date-picker-config 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Number Input Options Section -->
@if($selectedElement['type'] === 'number')
    <x-form-builder.number-options 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Password Options Section -->
@if($selectedElement['type'] === 'password')
    <x-form-builder.password-options 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- File Upload Options Section -->
@if($selectedElement['type'] === 'file')
    <x-form-builder.file-options 
        :selectedElement="$selectedElement"
        :selectedElementIndex="$selectedElementIndex"
    />
@endif

<!-- Validation Section -->
<x-form-builder.validation-settings 
    :selectedElement="$selectedElement"
    :selectedElementIndex="$selectedElementIndex"
    :availableValidationRules="$availableValidationRules"
/> 