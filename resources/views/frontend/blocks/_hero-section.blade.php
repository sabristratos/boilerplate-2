@props([
    'block',
    'data' => [],
    'preview' => false,
])

@php
    $blockData = $data ?: ($block ? $block->getTranslatedData() : []);
    $textAlignment = $blockData['text_alignment'] ?? 'center';
    $contentWidth = $blockData['content_width'] ?? 'max-w-4xl';
    $padding = $blockData['padding'] ?? 'py-24';
    $backgroundOverlay = $blockData['background_overlay'] ?? 70;
@endphp

<div class="relative {{ $padding }} w-full flex items-center justify-center overflow-hidden">
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
    <div class="absolute top-0 left-0 w-full h-full bg-black" style="opacity: {{ $backgroundOverlay / 100 }}"></div>

    <!-- Hero Content -->
    <div class="relative z-10 px-4 w-full">
        <div class="{{ $contentWidth }} mx-auto text-{{ $textAlignment }}">
            @if($blockData['overline'] ?? false)
                <div class="text-sm font-semibold text-blue-400 dark:text-blue-300 uppercase tracking-wide mb-2">
                    {{ $blockData['overline'] }}
                </div>
            @endif

            @if($blockData['heading'] ?? false)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
                    {!! $blockData['heading'] !!}
                </h1>
            @endif

            @if($blockData['subheading'] ?? false)
                <p class="text-xl md:text-2xl text-zinc-200 mb-8 max-w-3xl {{ $textAlignment === 'center' ? 'mx-auto' : '' }}">
                    {{ $blockData['subheading'] }}
                </p>
            @endif

            <div class="{{ $preview ? 'mt-2' : 'mt-8' }} flex flex-col sm:flex-row gap-4 {{ $textAlignment === 'center' ? 'justify-center' : ($textAlignment === 'right' ? 'justify-end' : 'justify-start') }}">
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
    </div>
</div>
