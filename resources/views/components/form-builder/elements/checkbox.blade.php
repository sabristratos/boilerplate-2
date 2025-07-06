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
    <flux:checkbox.group 
        label="{{ $properties['label'] }}"
        :wire:model="$wireModel"
        :required="$required"
    >
        @foreach($optionArray as $option)
            <flux:checkbox label="{{ $option }}" value="{{ $option }}" />
        @endforeach
    </flux:checkbox.group>
@else
    <flux:field variant="inline">
        <flux:checkbox 
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
