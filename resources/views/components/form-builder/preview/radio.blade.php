<flux:radio.group 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    required="{{ $required ? 'true' : '' }}"
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    description-trailing="{{ $properties['descriptionTrailing'] ?? false ? 'true' : 'false' }}"
>
    @foreach($options as $option)
        <flux:radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</flux:radio.group> 