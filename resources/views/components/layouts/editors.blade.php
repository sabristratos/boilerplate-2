<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 font-sans antialiased">
        <main>
            {{ $slot }}
        </main>

        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @livewire('confirmation-modal')  
        @fluxScripts
    </body>
</html> 