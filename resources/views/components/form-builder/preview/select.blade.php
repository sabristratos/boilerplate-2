<flux:select 
    wire:model="previewFormData.{{ $fieldName }}" 
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    required="{{ $required ? 'true' : '' }}"
>
    @foreach($options as $option)
        <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
    @endforeach
</flux:select>
@error("previewFormData.{$fieldName}")
    <flux:error>{{ $message }}</flux:error>
@enderror 