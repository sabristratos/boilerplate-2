@props([
    'block',
    'data' => [],
])

<section class="relative h-screen w-full flex items-center justify-center text-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center" style="background-image: url('{{ $block->getFirstMediaUrl('background_image') ?: 'https://images.unsplash.com/photo-1543393716-375f47996a77?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D' }}')"></div>
    <div class="absolute top-0 left-0 w-full h-full bg-black/70"></div>

    <!-- Hero Content -->
    <div class="relative z-10 px-4">
        @if($data['overline'] ?? false)
            <x-frontend.text style="overline" font="heading">
                {{ $data['overline'] }}
            </x-frontend.text>
        @endif

        @if($data['heading'] ?? false)
            <x-frontend.heading as="h1" style="display" class="my-2 max-w-3xl mx-auto md:my-4">
                {!! $data['heading'] !!}
            </x-frontend.heading>
        @endif

        @if($data['subheading'] ?? false)
            <x-frontend.text style="lede" class="mt-4">
                {{ $data['subheading'] }}
            </x-frontend.text>
        @endif

        <div class="mt-8 flex flex-col sm:flex-row justify-center items-center gap-4">
            @foreach($data['buttons'] ?? [] as $button)
                <x-frontend.button
                    :href="$button['url'] ?? '#'"
                    :variant="$button['variant'] ?? 'primary'"
                >
                    {{ $button['text'] }}
                </x-frontend.button>
            @endforeach
        </div>
    </div>
</section>
