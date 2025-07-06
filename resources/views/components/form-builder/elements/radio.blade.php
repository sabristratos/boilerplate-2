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
    }
@endphp

<flux:radio.group 
    label="{{ $properties['label'] ?? '' }}"
    variant="{{ $fluxProps['variant'] ?? 'default' }}"
>
    @foreach($parsedOptions as $option)
        <flux:radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</flux:radio.group>
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
    }
@endphp

@if($isPreview && !empty($optionArray))
    <flux:radio.group 
        :wire:model="$wireModel"
        :label="$properties['label']"
        :required="$required"
    >
        @foreach($optionArray as $option)
            <flux:radio value="{{ $option }}" label="{{ $option }}" />
        @endforeach
    </flux:radio.group>
@else
    <flux:field variant="inline">
        <flux:radio 
            :wire:model="$wireModel"
            :required="$required"
        />
        <flux:label>{{ $properties['label'] }}</flux:label>
    </flux:field>
@endif

@if($isPreview && $fieldName)
    @error("formData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
