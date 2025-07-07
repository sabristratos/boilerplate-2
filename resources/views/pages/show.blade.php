<x-layouts.guest :page="$page">
    @foreach($page->contentBlocks()->ordered()->get() as $block)
        @if($block->isVisible())
            @php
                $blockClass = $blockManager->find($block->type);
                
                // Get block data from the latest revision or fall back to current model data
                $latestRevision = $block->latestRevision();
                $blockData = $latestRevision && isset($latestRevision->data['data'])
                    ? $latestRevision->data['data']
                    : $block->getTranslatedData(app()->getLocale());
                
                $blockSettings = $latestRevision && isset($latestRevision->data['settings'])
                    ? $latestRevision->data['settings']
                    : $block->getSettingsArray();
                
                $data = array_merge($blockData, $blockSettings);
            @endphp

            @if($blockClass)
                @include($blockClass->getFrontendView(), [
                    'block' => $block, 
                    'data' => $data,
                    'has_draft_changes' => false, // No longer using draft system
                    'draft_settings' => $blockSettings,
                ])
            @endif
        @endif
    @endforeach
</x-layouts.guest> 