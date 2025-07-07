@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
    $error = $errors->first("previewFormData.{$fieldName}");
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
@endphp
<x-forms.input 
    wireModel="previewFormData.{{ $fieldName }}" 
    type="password" 
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
