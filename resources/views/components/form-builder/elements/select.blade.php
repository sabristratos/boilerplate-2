@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
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
    }
@endphp

<x-forms.select 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    wireModel="{{ $wireModel }}"
    :required="$required"
    :clearable="$fluxProps['clearable'] ?? false"
    :searchable="$fluxProps['searchable'] ?? false"
    :multiple="$fluxProps['multiple'] ?? false"
    variant="{{ $fluxProps['variant'] ?? 'default' }}"
    :error="$isPreview && $fieldName ? $errors->first("formData.{$fieldName}") : null"
>
    @foreach($optionArray as $option)
        <x-forms.select-option value="{{ $option }}">{{ $option }}</x-forms.select-option>
    @endforeach
</x-forms.select>
