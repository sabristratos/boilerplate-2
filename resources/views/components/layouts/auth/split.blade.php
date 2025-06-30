<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative overflow-hidden grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <img src="https://images.unsplash.com/photo-1550684393-8e0b1468ca57?q=80&w=686&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Door" class="object-cover absolute inset-0 h-full w-full" />
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
                <div class="relative hidden w-0 flex-1 lg:block">
                    <div class="absolute top-0 left-0 flex items-center justify-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 font-medium" wire:navigate>
                            <x-app-logo class="me-2 h-7 fill-current text-white" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:max-w-[375px]">
                    <div class="flex items-center gap-2 text-2xl font-semibold lg:hidden">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 font-medium" wire:navigate>
                            <x-app-logo class="size-9 fill-current text-black dark:text-white" />
                        </a>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @persist('toast')
        <flux:toast position="bottom right" />
        @endpersist
        @fluxScripts
    </body>
</html>
