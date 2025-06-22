@props(['block', 'alpine' => false])

<div class="bg-zinc-100 dark:bg-zinc-800 p-8 rounded-lg">
    @if ($alpine)
        <h1 class="text-4xl font-bold text-center" x-text="state.heading"></h1>
        <p class="text-lg text-center mt-2" x-text="state.subheading"></p>
    @else
        <h1 class="text-4xl font-bold text-center">{{ $block->data['heading'] ?? '' }}</h1>
        <p class="text-lg text-center mt-2">{{ $block->data['subheading'] ?? '' }}</p>
    @endif
    
    @if($block->hasMedia('images'))
        <div class="mt-4">
            {{ $block->getFirstMedia('images') }}
        </div>
    @endif
</div> 