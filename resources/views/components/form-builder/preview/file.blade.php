<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="file" 
    label="{{ $label }}"
    multiple="{{ $properties['multiple'] ?? false ? 'true' : '' }}"
    accept="{{ $properties['accept'] ?? '' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 