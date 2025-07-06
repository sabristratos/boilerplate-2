<?php

declare(strict_types=1);

namespace App\Actions\Content;

use App\Models\Page;
use App\Services\BlockManager;

/**
 * Action for creating new content blocks on a page.
 *
 * This action handles the creation of new content blocks, including
 * setting up default data and translations for all available locales.
 */
class CreateContentBlockAction
{
    /**
     * Create a new CreateContentBlockAction instance.
     */
    public function __construct(protected BlockManager $blockManager) {}

    /**
     * Execute the action to create a new content block.
     *
     * @param  Page  $page  The page to add the block to
     * @param  string  $type  The type of block to create
     * @param  array<string, string>  $availableLocales  Available locales for the application
     * @return \App\Models\ContentBlock The created content block
     *
     * @throws \Exception If the block type is invalid
     */
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
