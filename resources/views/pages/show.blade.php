<x-layouts.guest :title="$page->title">
    @foreach($page->contentBlocks->where('status', 'published') as $block)
        @if($block->block_class)
            @include($block->block_class->getFrontendView(), ['block' => $block])
        @endif
    @endforeach
</x-layouts.guest> 