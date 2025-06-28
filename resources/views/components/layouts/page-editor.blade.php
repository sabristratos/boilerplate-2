<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-100 dark:bg-zinc-900">
<head>
    @include('partials.head')
    @livewireStyles
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full">
        <header class="bg-white dark:bg-zinc-800/50 backdrop-blur-md shadow-sm sticky top-0 z-20">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ route('admin.pages.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">{{ __('navigation.pages') }}</span>
                        </a>
                    </div>

                    @if(isset($page))
                        <div>
                            <h1 class="text-lg font-semibold text-zinc-900 dark:text-white truncate" title="{{ $page->title }}">
                                {{ $page->title }}
                            </h1>
                        </div>
                    @endif

                    <div class="flex items-center">
                        @if(isset($page))
                            <a href="{{ route('pages.show', $page) }}" target="_blank" class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors">
                                <span class="text-sm font-medium">{{ __('navigation.view_page') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M6.194 12.75a.75.75 0 0 0 1.06 0l6.25-6.25a.75.75 0 0 0-1.06-1.06L7.25 11.694 6.194 12.75Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M11.75 5a.75.75 0 0 0 .75-.75V4.25a.75.75 0 0 0-1.5 0V5a.75.75 0 0 0 .75.75Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M13.25 6.75a.75.75 0 0 0 .75-.75V4.75a.75.75 0 0 0-1.5 0v1.25c0 .414.336.75.75.75Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M14.75 8.25a.75.75 0 0 0 .75-.75V6.25a.75.75 0 0 0-1.5 0v1.25c0 .414.336.75.75.75Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M16.25 9.75a.75.75 0 0 0 .75-.75V7.75a.75.75 0 0 0-1.5 0v1.25c0 .414.336.75.75.75Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M17.75 11.25a.75.75 0 0 0 .75-.75V9.25a.75.75 0 0 0-1.5 0v1.25c0 .414.336.75.75.75Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="py-10">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html> 