<?php

namespace App\Actions\Content;

use App\Enums\ContentBlockStatus;
use App\Models\Page;
use App\Services\BlockManager;

class CreateContentBlockAction
{
    public function __construct(protected BlockManager $blockManager)
    {
    }

    public function execute(Page $page, string $type, array $availableLocales): \App\Models\ContentBlock
    {
        $blockClass = $this->blockManager->find($type);

        if (! $blockClass) {
            throw new \Exception('Invalid block type.');
        }

        $block = $page->contentBlocks()->create([
            'type' => $blockClass->getType(),
            'status' => ContentBlockStatus::DRAFT,
        ]);

        $defaultData = $blockClass->getDefaultData();

        foreach ($availableLocales as $localeCode => $localeName) {
            $block->setTranslation('data', $localeCode, $defaultData);
        }
        $block->save();

        return $block;
    }
} 