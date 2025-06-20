<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

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
