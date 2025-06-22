<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 font-sans antialiased">
        <div class="container mx-auto py-8 px-4">
            {{ $slot }}
        </div>

        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @fluxScripts
    </body>
</html> 