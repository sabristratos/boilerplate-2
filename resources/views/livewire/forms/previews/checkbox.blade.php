@php
    $attributes = new \Illuminate\View\ComponentAttributeBag();
    $options = $field->component_options ?? [];
    foreach ($options as $key => $value) {
        if ($key === 'tooltip' && !empty($value)) continue;

        if (is_null($value)) continue;

        $attributes = $attributes->merge([$key => $value]);
    }
@endphp

<div
    @if (!empty($field->component_options['tooltip']))
        x-data="{}"
        x-tooltip.raw="{{ $field->component_options['tooltip'] }}"
    @endif
>
    @if(is_array($field->options) && !empty($field->options))
        <flux:checkbox.group
            :label="$field->label"
            :required="$field->isRequired()"
            {{ $attributes }}
        >
            @foreach($field->options as $option)
                <flux:checkbox value="{{ $option['value'] }}" :label="$option['label']" />
            @endforeach
        </flux:checkbox.group>
    @else
        <flux:checkbox
            :label="$field->label"
            :required="$field->isRequired()"
            {{ $attributes }}
        />
    @endif
</div> 