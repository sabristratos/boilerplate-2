<flux:checkbox.group 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}{{ $required ? ' *' : '' }}"
    required="{{ $required ? 'true' : '' }}"
    badge="{{ $properties['badge'] ?? '' }}"
    description="{{ $properties['description'] ?? '' }}"
    description-trailing="{{ $properties['descriptionTrailing'] ?? false ? 'true' : 'false' }}"
>
    @foreach($options as $option)
        <flux:checkbox value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</flux:checkbox.group>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 