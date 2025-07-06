@php
    $icon = !empty($properties['fluxProps']['icon'] ?? '') ? $properties['fluxProps']['icon'] : null;
    $iconTrailing = !empty($properties['fluxProps']['iconTrailing'] ?? '') ? $properties['fluxProps']['iconTrailing'] : null;
@endphp
<flux:select 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : '' }}"
    @if($icon) icon="{{ $icon }}" @endif
    @if($iconTrailing) icon:trailing="{{ $iconTrailing }}" @endif
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    description-trailing="{{ $properties['descriptionTrailing'] ?? false ? 'true' : 'false' }}"
    clearable="{{ $properties['fluxProps']['clearable'] ?? false ? 'true' : '' }}"
    searchable="{{ $properties['fluxProps']['searchable'] ?? false ? 'true' : '' }}"
    multiple="{{ $properties['fluxProps']['multiple'] ?? false ? 'true' : '' }}"
    variant="{{ $properties['fluxProps']['variant'] ?? 'default' }}"
>
    @foreach($options as $option)
        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
    @endforeach
</flux:select>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 