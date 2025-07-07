@props([
    'value' => '',
    'selected' => false,
    'disabled' => false,
])

<option 
    value="{{ $value }}" 
    @if($selected) selected @endif
    @if($disabled) disabled @endif
    {{ $attributes }}
>
    {{ $slot }}
</option> 