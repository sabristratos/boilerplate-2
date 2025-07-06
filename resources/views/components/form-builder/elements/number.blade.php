@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $min = $properties['min'] ?? '';
    $max = $properties['max'] ?? '';
    $step = $properties['step'] ?? '1';
    $clearable = $properties['clearable'] ?? true;
    $copyable = $properties['copyable'] ?? false;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "previewFormData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
    
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
<<<<<<< HEAD
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
=======
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
        icon="{{ $fluxProps['icon'] }}"
        :wire:model="$wireModel"
        :required="$required"
        :min="$properties['min'] !== '' ? $properties['min'] : null"
        :max="$properties['max'] !== '' ? $properties['max'] : null"
        :step="$properties['step'] !== '' ? $properties['step'] : null"
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@elseif($hasIcon)
    <flux:input 
        type="number"
<<<<<<< HEAD
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
=======
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
        icon="{{ $fluxProps['icon'] }}"
        :wire:model="$wireModel"
        :required="$required"
        :min="$properties['min'] !== '' ? $properties['min'] : null"
        :max="$properties['max'] !== '' ? $properties['max'] : null"
        :step="$properties['step'] !== '' ? $properties['step'] : null"
    />
@elseif($hasIconTrailing)
    <flux:input 
        type="number"
<<<<<<< HEAD
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        {!! $attributesString !!}
=======
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        :wire:model="$wireModel"
        :required="$required"
        :min="$properties['min'] !== '' ? $properties['min'] : null"
        :max="$properties['max'] !== '' ? $properties['max'] : null"
        :step="$properties['step'] !== '' ? $properties['step'] : null"
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    >
        <x-slot name="icon:trailing">
            <flux:icon name="{{ $fluxProps['iconTrailing'] }}" />
        </x-slot>
    </flux:input>
@else
    <flux:input 
        type="number"
<<<<<<< HEAD
        label="{{ $properties['label'] ?? '' }}" 
        placeholder="{{ $properties['placeholder'] ?? '' }}"
        step="{{ $step }}"
        :clearable="$clearable"
        :copyable="$copyable"
        {!! $attributesString !!}
=======
        label="{{ $properties['label'] }}" 
        placeholder="{{ $properties['placeholder'] }}"
        :clearable="$fluxProps['clearable'] ?? false"
        :copyable="$fluxProps['copyable'] ?? false"
        :viewable="$fluxProps['viewable'] ?? false"
        :wire:model="$wireModel"
        :required="$required"
        :min="$properties['min'] !== '' ? $properties['min'] : null"
        :max="$properties['max'] !== '' ? $properties['max'] : null"
        :step="$properties['step'] !== '' ? $properties['step'] : null"
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
    />
@endif

@if($isPreview && $fieldName)
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
@endif 