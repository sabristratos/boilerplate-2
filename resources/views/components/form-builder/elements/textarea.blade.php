@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

<x-forms.textarea 
    label="{{ $properties['label'] }}" 
    placeholder="{{ $properties['placeholder'] }}"
    :clearable="$fluxProps['clearable'] ?? false"
    :copyable="$fluxProps['copyable'] ?? false"
    :viewable="$fluxProps['viewable'] ?? false"
    icon="{{ $fluxProps['icon'] ?? null }}"
    iconTrailing="{{ $fluxProps['iconTrailing'] ?? null }}"
    wireModel="{{ $wireModel }}"
    :required="$required"
    :error="$isPreview && $fieldName ? $errors->first("formData.{$fieldName}") : null"
/>
