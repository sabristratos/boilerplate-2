@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'lg',
])

@php
    $tag = $href ? 'a' : 'button';

    $baseClasses = 'font-bold uppercase tracking-wider transition-all duration-300 transform inline-block rounded-md';

    $variantClasses = [
        'primary' => 'bg-primary hover:bg-primary-dark text-white hover:scale-105',
        'secondary' => 'bg-zinc-600 hover:bg-zinc-700 text-white hover:scale-105',
        'ghost' => 'bg-transparent hover:bg-white/10 text-white hover:scale-105 border border-white/20',
        'outline' => 'bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900',
    ];

    $sizeClasses = [
        'lg' => 'px-6 py-2',
        'md' => 'px-4 py-1',
        'sm' => 'px-2 py-2',
    ];

    $classes = implode(' ', [
        $baseClasses,
        \Illuminate\Support\Arr::get($variantClasses, $variant, $variantClasses['primary']),
        \Illuminate\Support\Arr::get($sizeClasses, $size, $sizeClasses['lg']),
    ]);
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $tag }}> 