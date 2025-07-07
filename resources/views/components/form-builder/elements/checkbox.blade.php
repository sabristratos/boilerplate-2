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

@if($isPreview && !empty($optionArray))
    <x-forms.checkbox-group 
        label="{{ $properties['label'] }}"
        wireModel="{{ $wireModel }}"
        :required="$required"
        variant="{{ $fluxProps['variant'] ?? 'default' }}"
        :error="$errors->first("formData.{$fieldName}")"
    >
        @foreach($optionArray as $option)
            <x-forms.checkbox label="{{ $option }}" value="{{ $option }}" />
        @endforeach
    </x-forms.checkbox-group>
@else
    <x-forms.checkbox 
        wireModel="{{ $wireModel }}"
        :required="$required"
        label="{{ $properties['label'] }}"
        :error="$errors->first("formData.{$fieldName}")"
    />
@endif
