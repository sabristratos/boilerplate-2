@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
@endphp
<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="file" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    required="{{ $required ? 'true' : '' }}"
    multiple="{{ $properties['multiple'] ?? false ? 'true' : '' }}"
    accept="{{ $properties['accept'] ?? '' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) icon:trailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    description-trailing="{{ $properties['descriptionTrailing'] ?? false ? 'true' : 'false' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 