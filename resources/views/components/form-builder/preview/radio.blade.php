<flux:field>
    <flux:label>{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</flux:label>
    <flux:radio.group 
        wire:model="previewFormData.{{ $fieldName }}" 
        label="{{ $label }}"
        required="{{ $required ? 'true' : '' }}"
    >
        @foreach($options as $option)
            <flux:radio value="{{ $option }}" label="{{ $option }}" />
        @endforeach
    </flux:radio.group>
    @error("previewFormData.{$fieldName}")
        <flux:error>{{ $message }}</flux:error>
    @enderror
</flux:field> 