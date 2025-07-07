<flux:input 
    wire:model="previewFormData.{{ $fieldName }}" 
    type="number" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    min="{{ $min }}"
    max="{{ $max }}"
    step="{{ $step }}"
    required="{{ $required ? 'true' : '' }}"
/>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 
