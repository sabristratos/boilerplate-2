@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
    $error = isset($errors) ? $errors->first("previewFormData.{$fieldName}") : null;
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
    $multiple = $properties['multiple'] ?? false;
    $showPreview = $properties['showPreview'] ?? false;
@endphp
<x-forms.file 
    wireModel="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    required="{{ $required ? 'true' : 'false' }}"
    multiple="{{ $multiple ? 'true' : 'false' }}"
    accept="{{ $properties['accept'] ?? '' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) iconTrailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    descriptionTrailing="{{ $descriptionTrailing ? 'true' : 'false' }}"
    maxSize="{{ $properties['maxSize'] ?? '' }}"
    showPreview="{{ $showPreview ? 'true' : 'false' }}"
    error="{{ $error }}"
/> 
