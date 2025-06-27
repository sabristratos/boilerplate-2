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
    <flux:input
        type="file"
        :label="$field->label"
        :required="$field->isRequired()"
        {{ $attributes }}
    />
</div> 