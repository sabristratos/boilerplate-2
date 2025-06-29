@props([
    'as' => 'h2',
    'style' => 'h2',
])

@php
    $styles = [
        'display' => 'font-heading text-5xl sm:text-6xl md:text-7xl lg:text-8xl uppercase leading-tight text-white',
        'h1' => 'font-heading text-4xl sm:text-5xl md:text-6xl uppercase leading-tight text-white',
        'h2' => 'font-heading text-3xl sm:text-4xl uppercase text-white',
        'h3' => 'font-heading text-2xl sm:text-3xl uppercase text-white',
    ];
    $classes = \Illuminate\Support\Arr::get($styles, $style, $styles['h2']);
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}> 