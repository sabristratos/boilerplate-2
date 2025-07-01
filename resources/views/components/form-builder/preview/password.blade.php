<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="password" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 