<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 font-sans antialiased">
        <header class="bg-white dark:bg-zinc-800 shadow-sm">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex">
                        <a href="{{ route('home') }}">
                            <x-app-logo class="h-8 w-auto" />
                        </a>
                    </div>
                    <nav class="hidden md:flex space-x-8">
                        @foreach($headerLinks as $link)
                            <a href="{{ $link['url'] }}" class="text-base font-medium text-gray-500 hover:text-gray-900">{{ $link['label'] }}</a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </header>

        <div>
            {{ $slot }}
        </div>

        <footer class="bg-white dark:bg-zinc-800">
            <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <nav class="flex flex-wrap justify-center -mx-5 -my-2">
                    @foreach($footerLinks as $link)
                        <div class="px-5 py-2">
                            <a href="{{ $link['url'] }}" class="text-base text-gray-500 hover:text-gray-900">
                                {{ $link['label'] }}
                            </a>
                        </div>
                    @endforeach
                </nav>
                <p class="mt-8 text-center text-base text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('general.all_rights_reserved') }}</p>
            </div>
        </footer>

        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @fluxScripts

        <x-admin-bar :page="$page ?? null" />
    </body>
</html> 