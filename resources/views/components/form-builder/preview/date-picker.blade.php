<flux:field>
    <flux:label>{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</flux:label>
    <flux:date-picker 
        wire:model="previewFormData.{{ $fieldName }}" 
        placeholder="{{ $placeholder }}"
        required="{{ $required ? 'true' : '' }}"
    >
        <x-slot name="trigger">
            <flux:date-picker.input />
        </x-slot>
    </flux:date-picker>
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
</flux:field> 