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
    <x-forms.radio-group 
        wireModel="{{ $wireModel }}"
        label="{{ $properties['label'] }}"
        :required="$required"
        variant="{{ $fluxProps['variant'] ?? 'default' }}"
        :error="$errors->first("formData.{$fieldName}")"
    >
        @foreach($optionArray as $option)
            <x-forms.radio value="{{ $option }}" label="{{ $option }}" />
        @endforeach
    </x-forms.radio-group>
@else
    <x-forms.radio 
        wireModel="{{ $wireModel }}"
        :required="$required"
        label="{{ $properties['label'] }}"
        :error="$errors->first("formData.{$fieldName}")"
    />
@endif
