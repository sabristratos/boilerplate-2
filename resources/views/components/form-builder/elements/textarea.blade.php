@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

@if($hasIcon && $hasIconTrailing)
    <flux:textarea 
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
    </flux:textarea>
@elseif($hasIcon)
    <flux:textarea 
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
    <flux:textarea 
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
    </flux:textarea>
@else
    <flux:textarea 
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
    @error("formData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif
