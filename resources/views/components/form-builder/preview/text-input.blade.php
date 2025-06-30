<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="{{ $type }}" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : '' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 