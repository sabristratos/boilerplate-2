<flux:textarea 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : '' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 