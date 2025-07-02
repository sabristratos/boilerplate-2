<x-layouts.guest>
    <div class="flex flex-col items-center justify-center min-h-[70vh] py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-zinc-50 via-white to-zinc-100 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900">
        <div class="max-w-xl w-full text-center">
            <x-frontend.heading as="h1" class="text-4xl sm:text-5xl font-extrabold tracking-tight text-zinc-900 dark:text-white mb-4">
                Welcome to {{ config('app.name', 'Your Website') }}
            </x-frontend.heading>
            <p class="mt-2 text-lg text-zinc-600 dark:text-zinc-300 mb-8">
                A minimalist Laravel starter kit for your next big idea.<br>
                Effortless. Elegant. Ready to launch.
            </p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold shadow hover:bg-zinc-700 hover:dark:bg-zinc-200 transition">
                Get Started
            </a>
        </div>
    </div>
</x-layouts.guest>