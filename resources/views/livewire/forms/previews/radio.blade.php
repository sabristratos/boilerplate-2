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
    <flux:radio.group
        :label="$field->label"
        :required="$field->isRequired()"
        {{ $attributes }}
    >
        @if(is_array($field->options))
            @foreach($field->options as $option)
                <flux:radio value="{{ $option['value'] }}" :label="$option['label']" />
            @endforeach
        @endif
    </flux:radio.group>
</div> 