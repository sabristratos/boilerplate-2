<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@isset($page)
    <title>{{ $page->getTranslation('meta_title', app()->getLocale()) ?: $page->getTranslation('title', app()->getLocale()) }} | {{ config('app.name') }}</title>
    @if($page->getTranslation('meta_description', app()->getLocale()))
        <meta name="description" content="{{ $page->getTranslation('meta_description', app()->getLocale()) }}">
    @endif
    @if($page->no_index)
        <meta name="robots" content="noindex, nofollow">
    @endif
@else
    <title>{{ $title ?? config('app.name') }}</title>
@endisset

<meta name="csrf-token" content="{{ csrf_token() }}">

    <link id="favicon" rel="icon" href="{{ setting('appearance.favicon', '/favicon.png') }}" sizes="any">
    <link id="apple-touch-icon" rel="apple-touch-icon" href="{{ setting('appearance.favicon', '/favicon.png') }}">

    <style>
        :root {
            --color-accent: {{ setting('appearance.primary_color', 'oklch(64.5% .246 16.439)') }};
        }
    </style>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@if(setting('appearance.theme', 'light') === 'light')
    @fluxAppearance
@endif
