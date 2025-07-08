@props(['element', 'properties', 'fluxProps', 'mode' => 'edit', 'fieldName' => null])

@php
    // Ensure $errors is always defined
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $hasIcon = $fluxProps['icon'] ?? false;
    $hasIconTrailing = $fluxProps['iconTrailing'] ?? false;
    $type = $element->type === \App\Enums\FormElementType::EMAIL->value ? 'email' : 'text';
    $isPreview = $mode === 'preview';
    $wireModel = $isPreview && $fieldName ? "formData.{$fieldName}" : null;
    $required = $isPreview ? (in_array('required', $properties['validation']['rules'] ?? []) ? 'true' : '') : '';
@endphp

<x-forms.input 
    type="{{ $type }}"
    label="{{ $properties['label'] ?? '' }}" 
    placeholder="{{ $properties['placeholder'] ?? '' }}"
    :clearable="$fluxProps['clearable'] ?? false"
    :copyable="$fluxProps['copyable'] ?? false"
    :viewable="$fluxProps['viewable'] ?? false"
    icon="{{ $fluxProps['icon'] ?? null }}"
    iconTrailing="{{ $fluxProps['iconTrailing'] ?? null }}"
    wireModel="{{ $wireModel }}"
    :required="$required"
    :error="$isPreview && $fieldName ? $errors->first("formData.{$fieldName}") : null"
/>
