@props([
    'block',
    'data' => [],
    'preview' => false,
])

@php
    $blockData = $block ? $block->getTranslatedData() : $data;
@endphp

<section class="relative {{ $preview ? 'h-32' : 'h-screen' }} w-full flex items-center justify-center text-center overflow-hidden">
    <!-- Background Image with Overlay -->
    @php
        $backgroundImage = config('app.default_hero_background', 'https://images.unsplash.com/photo-1543393716-375f47996a77?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        
        // Check for block-specific background image
        if ($block && !$preview) {
            $mediaUrl = $block->getFirstMediaUrl('background_image');
            if (!empty($mediaUrl)) {
                $backgroundImage = $mediaUrl;
            } elseif (!empty($blockData['background_image'] ?? null)) {
                $backgroundImage = $blockData['background_image'];
            }
        }
    @endphp
    <div class="absolute top-0 left-0 w-full h-full bg-cover bg-center" style="background-image: url('{{ $backgroundImage }}')"></div>
    <div class="absolute top-0 left-0 w-full h-full bg-black/70"></div>

    <!-- Hero Content -->
    <div class="relative z-10 px-4">
        @if($blockData['overline'] ?? false)
            <x-frontend.text style="overline" font="heading">
                {{ $blockData['overline'] }}
            </x-frontend.text>
        @endif

        @if($blockData['heading'] ?? false)
            <x-frontend.heading as="h1" style="display" class="my-2 max-w-3xl mx-auto md:my-4">
                {!! $blockData['heading'] !!}
            </x-frontend.heading>
        @endif

        @if($blockData['subheading'] ?? false)
            <x-frontend.text style="lede" class="mt-4">
                {{ $blockData['subheading'] }}
            </x-frontend.text>
        @endif

        <div class="{{ $preview ? 'mt-2' : 'mt-8' }} flex flex-col sm:flex-row justify-center items-center gap-4">
            @foreach($blockData['buttons'] ?? [] as $button)
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
