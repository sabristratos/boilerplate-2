<flux:radio.group 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
>
    @foreach($options as $option)
        <flux:radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
    @endforeach
</flux:radio.group> 