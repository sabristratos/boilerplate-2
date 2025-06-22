<div class="bg-zinc-100 dark:bg-zinc-800 p-8 rounded-lg">
    <h1 class="text-4xl font-bold">{{ $block->data['heading'] }}</h1>
    <p class="text-lg text-zinc-600 dark:text-zinc-400 mt-2">{{ $block->data['subheading'] }}</p>
    
    @if($block->hasMedia('images'))
        <div class="mt-4">
            {{ $block->getFirstMedia('images') }}
        </div>
    @endif
</div> 