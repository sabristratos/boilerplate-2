<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@isset($page)
    <title>{{ $page->getTranslation('meta_title', app()->getLocale()) ?: $page->getTranslation('title', app()->getLocale()) }} | {{ config('app.name') }}</title>
    
    {{-- Basic Meta Tags --}}
    @if($page->getTranslation('meta_description', app()->getLocale()))
        <meta name="description" content="{{ $page->getTranslation('meta_description', app()->getLocale()) }}">
    @endif
    
    @if($page->getTranslation('meta_keywords', app()->getLocale()))
        <meta name="keywords" content="{{ $page->getTranslation('meta_keywords', app()->getLocale()) }}">
    @endif
    
    {{-- Robots Meta Tags --}}
    @php
        $robots = [];
        if ($page->no_index) $robots[] = 'noindex';
        if ($page->no_follow) $robots[] = 'nofollow';
        if ($page->no_archive) $robots[] = 'noarchive';
        if ($page->no_snippet) $robots[] = 'nosnippet';
        $robotsContent = !empty($robots) ? implode(', ', $robots) : 'index, follow';
    @endphp
    <meta name="robots" content="{{ $robotsContent }}">
    
    {{-- Canonical URL --}}
    @if($page->getTranslation('canonical_url', app()->getLocale()))
        <link rel="canonical" href="{{ $page->getTranslation('canonical_url', app()->getLocale()) }}">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $page->getTranslation('og_title', app()->getLocale()) ?: $page->getTranslation('meta_title', app()->getLocale()) ?: $page->getTranslation('title', app()->getLocale()) }}">
    @if($page->getTranslation('og_description', app()->getLocale()))
        <meta property="og:description" content="{{ $page->getTranslation('og_description', app()->getLocale()) }}">
    @endif
    @if($page->getTranslation('og_image', app()->getLocale()))
        <meta property="og:image" content="{{ $page->getTranslation('og_image', app()->getLocale()) }}">
    @endif
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="{{ $page->getTranslation('twitter_card_type', app()->getLocale()) ?: 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $page->getTranslation('twitter_title', app()->getLocale()) ?: $page->getTranslation('meta_title', app()->getLocale()) ?: $page->getTranslation('title', app()->getLocale()) }}">
    @if($page->getTranslation('twitter_description', app()->getLocale()))
        <meta name="twitter:description" content="{{ $page->getTranslation('twitter_description', app()->getLocale()) }}">
    @endif
    @if($page->getTranslation('twitter_image', app()->getLocale()))
        <meta name="twitter:image" content="{{ $page->getTranslation('twitter_image', app()->getLocale()) }}">
    @endif
    @if(setting('seo.twitter_username'))
        <meta name="twitter:site" content="@{{ setting('seo.twitter_username') }}">
    @endif
    
    {{-- Structured Data --}}
    @if($page->getTranslation('structured_data', app()->getLocale()))
        <script type="application/ld+json">
            {!! $page->getTranslation('structured_data', app()->getLocale()) !!}
        </script>
    @endif
@else
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    @if(setting('seo.twitter_username'))
        <meta name="twitter:site" content="@{{ setting('seo.twitter_username') }}">
    @endif
@endisset

{{-- Google Analytics --}}
@if(setting('seo.google_analytics_id'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('seo.google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ setting('seo.google_analytics_id') }}');
    </script>
@endif

{{-- Google Tag Manager --}}
@if(setting('seo.google_tag_manager_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ setting('seo.google_tag_manager_id') }}');</script>
    <!-- End Google Tag Manager -->
@endif

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
