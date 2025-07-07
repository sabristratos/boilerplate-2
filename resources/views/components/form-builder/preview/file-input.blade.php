<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="file" 
    label="{{ $label }}"
    multiple="{{ $multiple ? 'true' : '' }}"
    required="{{ $required ? 'true' : '' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 
