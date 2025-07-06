@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
<<<<<<< HEAD
    $options = $properties['options'] ?? [];
    $parsedOptions = [];
    
    // Handle both string and array options
    if (is_array($options)) {
        foreach ($options as $option) {
            if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                $parsedOptions[] = $option;
            } elseif (is_string($option)) {
                $option = trim($option);
                if ($option === '') continue;
                if (str_contains($option, '|')) {
                    [$value, $label] = explode('|', $option, 2);
                    $parsedOptions[] = ['value' => trim($value), 'label' => trim($label)];
                } else {
                    $parsedOptions[] = ['value' => $option, 'label' => $option];
                }
            }
        }
=======
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
    
    // Parse options (one per line)
    $options = $properties['options'] ?? '';
    $optionArray = [];
    if (is_string($options)) {
        $optionArray = array_filter(explode(PHP_EOL, $options));
    } elseif (is_array($options)) {
        $optionArray = $options;
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    }
@endphp

<flux:select 
<<<<<<< HEAD
    label="{{ $properties['label'] ?? '' }}"
    placeholder="{{ $properties['placeholder'] ?? '' }}"
=======
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    :wire:model="$wireModel"
    :required="$required"
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    :clearable="$fluxProps['clearable'] ?? false"
    :copyable="$fluxProps['copyable'] ?? false"
    :viewable="$fluxProps['viewable'] ?? false"
>
<<<<<<< HEAD
    @foreach($parsedOptions as $option)
        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
=======
    @foreach($optionArray as $option)
        <flux:select.option>{{ $option }}</flux:select.option>
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    @endforeach
</flux:select>

@if($isPreview && $fieldName)
    @error("formData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif
