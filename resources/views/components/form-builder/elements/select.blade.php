@props(['element', 'properties', 'fluxProps'])

@php
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
    }
@endphp

<flux:select 
    label="{{ $properties['label'] ?? '' }}"
    placeholder="{{ $properties['placeholder'] ?? '' }}"
    :clearable="$fluxProps['clearable'] ?? false"
    :copyable="$fluxProps['copyable'] ?? false"
    :viewable="$fluxProps['viewable'] ?? false"
>
    @foreach($parsedOptions as $option)
        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
    @endforeach
</flux:select>
