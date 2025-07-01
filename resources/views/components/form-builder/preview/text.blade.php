<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="text" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 