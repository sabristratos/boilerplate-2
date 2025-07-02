<x-layouts.guest :page="$page">
    @foreach($page->contentBlocks->where('visible', true) as $block)
        @php($blockClass = app(\App\Services\BlockManager::class)->find($block->type))
        @if($blockClass)
            @include($blockClass->getFrontendView(), ['block' => $block, 'data' => array_merge($block->getTranslatedData(), $block->getSettingsArray())])
        @endif
    @endforeach
</x-layouts.guest> 