<flux:checkbox.group 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
    required="{{ $required ? 'true' : '' }}"
>
    @foreach($options as $option)
        <flux:checkbox value="{{ $option }}" label="{{ $option }}" />
    @endforeach
</flux:checkbox.group>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 