@props(['element', 'properties', 'fluxProps'])

@php
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $type = $element['type'] === 'email' ? 'email' : 'text';
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:input 
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@elseif($hasIcon)
    <flux:input 
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@else
    <flux:input 
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
    />
@endif
