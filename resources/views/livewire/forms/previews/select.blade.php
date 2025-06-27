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
    <flux:select
        :label="$field->label"
        :placeholder="$field->placeholder"
        :required="$field->isRequired()"
        {{ $attributes }}
    >
        @if(is_array($field->options))
            @foreach($field->options as $option)
                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'][app()->getLocale()] ?? $option['value'] }}</flux:select.option>
            @endforeach
        @endif
    </flux:select>
</div>
