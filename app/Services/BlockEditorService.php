<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentBlock;

/**
 * Service for handling block editor business logic.
 *
 * This service encapsulates all the business logic related to editing content blocks,
 * including data loading, and state management.
 */
class BlockEditorService
{
    /**
     * Block manager service instance.
     */
    protected BlockManager $blockManager;

    /**
     * Constructor.
     */
    public function __construct(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * Load block data for editing.
     *
     * @param  ContentBlock  $block  The content block to load data for
     * @param  string  $locale  The active locale for editing
     * @return array<string, mixed> The loaded block state data
     */
    public function loadBlockData(ContentBlock $block, string $locale): array
    {
        // Load block data from the latest revision if available, otherwise from the model itself
        $blockClass = $this->blockManager->find($block->type);
        $defaultData = $blockClass instanceof \App\Blocks\Block ? $blockClass->getDefaultData() : [];

        // For now, we get the data directly from the model attributes.
        // The parent component (PageManager) will be responsible for providing the state from its revision data.
        $blockData = $block->getTranslatedData($locale);
        $blockSettings = $block->getSettingsArray();

        return array_merge($defaultData, $blockData, $blockSettings);
    }

    /**
     * Update block state with repeater data.
     *
     * @param  array<string, mixed>  $currentState  The current block state
     * @param  string  $modelPath  The dot notation path to the model
     * @param  array  $items  The new items to set
     * @return array<string, mixed> The updated block state
     */
    public function updateRepeaterState(array $currentState, string $modelPath, array $items): array
    {
        // Convert dot notation to array path
        $pathParts = explode('.', $modelPath);
        $state = $currentState;
        $current = &$state;

        foreach ($pathParts as $part) {
            if (! isset($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }

        $current = $items;

        return $state;
    }

    /**
     * Update block state with media data.
     *
     * @param  array<string, mixed>  $currentState  The current block state
     * @param  ContentBlock  $block  The content block
     * @param  string  $collection  The media collection name
     * @return array<string, mixed> The updated block state
     */
    public function updateMediaState(array $currentState, ContentBlock $block, string $collection): array
    {
        // Get the media URL for the collection (will be null if removed)
        $media = $block->getFirstMedia($collection);
        $mediaUrl = $media ? $media->getUrl() : null;

        $state = $currentState;

        // Update the state with the media URL (or null if removed)
        // For hero section, this would be background_image
        if ($collection === 'background_image') {
            $state['background_image'] = $mediaUrl;
        }
        // Add more collections as needed
        // elseif ($collection === 'other_collection') {
        //     $state['other_field'] = $mediaUrl;
        // }

        return $state;
    }

    /**
     * Get block by ID.
     *
     * @param  int  $blockId  The block ID
     * @return ContentBlock|null The content block or null if not found
     */
    public function getBlockById(int $blockId): ?ContentBlock
    {
        return ContentBlock::find($blockId);
    }

    /**
     * Get block class for a content block.
     *
     * @param  ContentBlock  $block  The content block
     * @return \App\Blocks\Block|null The block class or null if not found
     */
    public function getBlockClass(ContentBlock $block): ?\App\Blocks\Block
    {
        return $this->blockManager->find($block->type);
    }

    /**
     * Check if a block exists and is valid for editing.
     *
     * @param  int  $blockId  The block ID
     * @return bool True if the block exists and is valid
     */
    public function isValidBlock(int $blockId): bool
    {
        $block = $this->getBlockById($blockId);

        return $block !== null;
    }

    /**
     * Get block visibility status.
     *
     * @param  ContentBlock  $block  The content block
     * @return bool True if the block is visible
     */
    public function getBlockVisibility(ContentBlock $block): bool
    {
        return $block->isVisible();
    }
}
