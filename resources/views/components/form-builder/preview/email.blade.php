<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="email" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 