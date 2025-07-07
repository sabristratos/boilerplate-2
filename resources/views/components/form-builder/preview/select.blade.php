@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
    $error = isset($errors) ? $errors->first("previewFormData.{$fieldName}") : null;
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
    $clearable = $properties['fluxProps']['clearable'] ?? false;
    $searchable = $properties['fluxProps']['searchable'] ?? false;
    $multiple = $properties['fluxProps']['multiple'] ?? false;
@endphp
<x-forms.select 
    wireModel="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : 'false' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) iconTrailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    descriptionTrailing="{{ $descriptionTrailing ? 'true' : 'false' }}"
    clearable="{{ $clearable ? 'true' : 'false' }}"
    searchable="{{ $searchable ? 'true' : 'false' }}"
    multiple="{{ $multiple ? 'true' : 'false' }}"
    variant="{{ $properties['fluxProps']['variant'] ?? 'default' }}"
    error="{{ $error }}"
>
    @foreach($options as $option)
        <x-forms.select-option value="{{ $option['value'] }}">{{ $option['label'] }}</x-forms.select-option>
    @endforeach
</x-forms.select> 
