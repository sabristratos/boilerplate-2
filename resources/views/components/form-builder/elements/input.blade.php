@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    // Ensure $errors is always defined
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $type = $element->type === 'email' ? 'email' : 'text';
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
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
        :wire:model="$wireModel"
        :required="$required"
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
        :wire:model="$wireModel"
        :required="$required"
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
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
        type="{{ $type }}"
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
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
