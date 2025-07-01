@props(['element', 'properties', 'fluxProps'])

@php
    $min = $properties['min'] ?? '';
    $max = $properties['max'] ?? '';
    $step = $properties['step'] ?? '1';
    $clearable = $properties['clearable'] ?? true;
    $copyable = $properties['copyable'] ?? false;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    
    // Build attributes string
    $attributes = [];
    if ($min) {
        $attributes[] = 'min="' . $min . '"';
    }
    if ($max) {
        $attributes[] = 'max="' . $max . '"';
    }
    $attributesString = implode(' ', $attributes);
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:input 
        type="number"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        icon="{{ $fluxProps['icon'] }}"
        {!! $attributesString !!}
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@elseif($hasIcon)
    <flux:input 
        type="number"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        icon="{{ $fluxProps['icon'] }}"
        {!! $attributesString !!}
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="number"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        {!! $attributesString !!}
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@else
    <flux:input 
        type="number"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        {!! $attributesString !!}
    />
@endif 