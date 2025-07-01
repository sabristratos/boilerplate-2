@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "previewFormData.{$fieldName}" : null;
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

<flux:select 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    :wire:model="$wireModel"
    :required="$required"
    :clearable="$fluxProps['clearable'] ?? false"
    :searchable="$fluxProps['searchable'] ?? false"
    :multiple="$fluxProps['multiple'] ?? false"
    variant="{{ $fluxProps['variant'] ?? 'default' }}"
>
    @foreach($optionArray as $option)
        <flux:select.option>{{ $option }}</flux:select.option>
    @endforeach
</flux:select>

@if($isPreview && $fieldName)
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif
