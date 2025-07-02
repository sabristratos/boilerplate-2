<?php

namespace App\Actions\Content;

use App\Models\Page;
use App\Services\BlockManager;

class CreateContentBlockAction
{
    public function __construct(protected BlockManager $blockManager) {}

    public function execute(Page $page, string $type, array $availableLocales): \App\Models\ContentBlock
    {
        $blockClass = $this->blockManager->find($type);

        if (! $blockClass instanceof \App\Blocks\Block) {
            throw new \Exception('Invalid block type.');
        }

        $block = $page->contentBlocks()->create([
            'type' => $blockClass->getType(),
            'visible' => true,
        ]);

        $defaultData = $blockClass->getDefaultData();

        foreach (array_keys($availableLocales) as $localeCode) {
            $block->setTranslation('data', $localeCode, $defaultData);
        }
        $block->save();

        return $block;
    }
}
