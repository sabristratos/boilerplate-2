<x-layouts.guest :page="$page">
    @foreach($page->contentBlocks->where('visible', true) as $block)
        @php($blockClass = app(\App\Services\BlockManager::class)->find($block->type))
        @if($blockClass)
            @php
                // Use draft data if available, otherwise use published data
                if ($block->hasDraftChanges()) {
                    $data = array_merge($block->getDraftTranslatedData(app()->getLocale()), $block->getDraftSettingsArray());
                } else {
                    $data = array_merge($block->getTranslatedData(app()->getLocale()), $block->getSettingsArray());
                }
                
                // Debug logging for contact blocks
                if ($block->type === 'contact') {
                    \Log::info('Contact block data', [
                        'block_id' => $block->id,
                        'has_draft_changes' => $block->hasDraftChanges(),
                        'draft_settings' => $block->getDraftSettingsArray(),
                        'published_settings' => $block->getSettingsArray(),
                        'final_data' => $data,
                        'form_id_in_data' => $data['form_id'] ?? 'not found'
                    ]);
                }
            @endphp
            @include($blockClass->getFrontendView(), ['block' => $block, 'data' => $data])
        @endif
    @endforeach
</x-layouts.guest> 