@props([
    'as' => 'p',
    'style' => 'body-md',
    'font' => 'sans',
])

@php
    $styles = [
        'overline' => 'text-lg md:text-xl tracking-widest uppercase text-white',
        'lede' => 'text-xl text-primary leading-relaxed tracking-wider',
        'body-lg' => 'text-lg text-gray-300 leading-relaxed',
        'body-md' => 'text-base text-gray-400 leading-relaxed',
    ];

    $fontClasses = [
        'heading' => 'font-heading',
        'sans' => 'font-sans',
    ];

    $classes = \Illuminate\Support\Arr::get($styles, $style, $styles['body-md'])
        . ' ' . \Illuminate\Support\Arr::get($fontClasses, $font, $fontClasses['sans']);
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}> 