@props(['element', 'properties', 'fluxProps'])

@php
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:textarea 
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:textarea>
@elseif($hasIcon)
    <flux:textarea 
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
    />
@elseif($hasIconTrailing)
    <flux:textarea 
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:textarea>
@else
    <flux:textarea 
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
    />
@endif
