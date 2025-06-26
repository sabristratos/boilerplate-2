@props(['block'])

@php
    $globalBlockId = $block->data['global_block_id'] ?? null;
    $globalBlock = $globalBlockId ? \App\Models\GlobalBlock::find($globalBlockId) : null;
@endphp

@if($globalBlock)
    @php
        $blockManager = app(\App\Services\BlockManager::class);
        $blockClass = $blockManager->find($globalBlock->type);
    @endphp

    @if($blockClass)
        {{-- Render the correct frontend view for the global block, passing its data. --}}
        @include($blockClass->getFrontendView(), ['block' => $globalBlock])
    @endif
@endif 