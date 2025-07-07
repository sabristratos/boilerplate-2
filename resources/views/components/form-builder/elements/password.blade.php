@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "previewFormData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
        :wire:model="$wireModel"
        :required="$required"
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
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        icon="{{ $fluxProps['icon'] }}"
        :wire:model="$wireModel"
        :required="$required"
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="password"
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        :wire:model="$wireModel"
        :required="$required"
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
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        :wire:model="$wireModel"
        :required="$required"
    />
@endif

@if($isPreview && $fieldName)
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif 
