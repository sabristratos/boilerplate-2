@props(['element', 'properties', 'fluxProps'])

@php
    $viewable = $properties['viewable'] ?? true;
    $clearable = $properties['clearable'] ?? true;
    $copyable = $properties['copyable'] ?? false;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :viewable="$viewable"
        :clearable="$clearable"
        :copyable="$copyable"
        icon="{{ $fluxProps['icon'] }}"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@elseif($hasIcon)
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :viewable="$viewable"
        :clearable="$clearable"
        :copyable="$copyable"
        icon="{{ $fluxProps['icon'] }}"
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :viewable="$viewable"
        :clearable="$clearable"
        :copyable="$copyable"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@else
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :viewable="$viewable"
        :clearable="$clearable"
        :copyable="$copyable"
    />
@endif 