@props(['element', 'properties', 'fluxProps'])

@php
    $options = $properties['options'] ?? [];
    if (is_string($options)) {
        $options = array_filter(explode(PHP_EOL, $options));
    }
    
    // Build options HTML
    $optionsHtml = '';
    foreach ($options as $option) {
        $optionsHtml .= '<flux:select.option>' . htmlspecialchars($option) . '</flux:select.option>';
    }
@endphp

<flux:select 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    :clearable="$fluxProps['clearable'] ?? false"
    :searchable="$fluxProps['searchable'] ?? false"
    :multiple="$fluxProps['multiple'] ?? false"
    variant="{{ $fluxProps['variant'] ?? 'default' }}"
>
    {!! $optionsHtml !!}
</flux:select>
