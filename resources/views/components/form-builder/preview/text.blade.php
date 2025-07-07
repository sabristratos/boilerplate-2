@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
    $error = isset($errors) ? $errors->first("previewFormData.{$fieldName}") : null;
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
@endphp
<x-forms.input 
    wireModel="previewFormData.{{ $fieldName }}" 
    type="text" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : 'false' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) iconTrailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    descriptionTrailing="{{ $descriptionTrailing ? 'true' : 'false' }}"
    error="{{ $error }}"
/> 