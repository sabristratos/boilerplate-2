<x-layouts.guest :page="$page">
    @foreach($page->contentBlocks->where('status', 'published') as $block)
        @php($blockClass = app(\App\Services\BlockManager::class)->find($block->type))
        @if($blockClass)
            @include($blockClass->getFrontendView(), ['block' => $block, 'data' => $block->data])
        @endif
    @endforeach
</x-layouts.guest> 