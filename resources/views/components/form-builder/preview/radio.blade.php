@php
    $error = isset($errors) ? $errors->first("previewFormData.{$fieldName}") : null;
    $descriptionTrailing = $properties['descriptionTrailing'] ?? false;
@endphp
<x-forms.radio-group 
    wireModel="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    required="{{ $required ? 'true' : 'false' }}"
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    descriptionTrailing="{{ $descriptionTrailing ? 'true' : 'false' }}"
    variant="{{ $properties['fluxProps']['variant'] ?? 'default' }}"
    error="{{ $error }}"
>
    @foreach($options as $option)
        <x-forms.radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</x-forms.radio-group> 
