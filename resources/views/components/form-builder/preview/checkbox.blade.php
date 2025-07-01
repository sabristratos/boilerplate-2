<flux:checkbox.group 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
>
    @foreach($options as $option)
        <flux:checkbox value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</flux:checkbox.group>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 