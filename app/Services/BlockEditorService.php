<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\Contracts\BlockEditorServiceInterface;
use Illuminate\Support\Collection;

/**
 * Service for handling block editor business logic.
 *
 * This service encapsulates all the business logic related to editing content blocks,
 * including data loading, and state management.
 */
class BlockEditorService implements BlockEditorServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        /**
         * Block manager service instance.
         */
        protected BlockManager $blockManager
    )
    {
    }

    /**
     * Load block data for editing.
     *
     * @param  ContentBlock  $block  The content block to load data for
     * @param  string  $locale  The active locale for editing
     * @return array<string, mixed> The loaded block state data
     */
    public function loadBlockData(ContentBlock $block, string $locale = 'en'): array
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
     * Update repeater state in array data.
     *
     * @param  array<string, mixed>  $currentState  The current block state
     * @param  string  $modelPath  The dot notation path to the model
     * @param  array  $items  The new items to set
     * @return array<string, mixed> The updated block state
     */
    public function updateRepeaterStateInArray(array $currentState, string $modelPath, array $items): array
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
        $mediaUrl = $media instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media ? $media->getUrl() : null;

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

        return $block instanceof \App\Models\ContentBlock;
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

    /**
     * Check if a block is valid.
     *
     * @param ContentBlock $block The block to validate
     * @return bool True if the block is valid
     */
    public function isBlockValid(ContentBlock $block): bool
    {
        return $this->isValidBlock($block->id);
    }

    /**
     * Update block visibility.
     *
     * @param ContentBlock $block The block to update
     * @param bool $visible The visibility status
     * @return ContentBlock The updated block
     */
    public function updateBlockVisibility(ContentBlock $block, bool $visible): ContentBlock
    {
        $block->update(['is_visible' => $visible]);
        return $block;
    }

    /**
     * Update repeater state for a block.
     *
     * @param ContentBlock $block The block containing the repeater
     * @param string $fieldName The repeater field name
     * @param array<string, mixed> $state The new repeater state
     * @return ContentBlock The updated block
     */
    public function updateRepeaterState(ContentBlock $block, string $fieldName, array $state): ContentBlock
    {
        $currentData = $block->getTranslatedData();
        $updatedData = $this->updateRepeaterStateInArray($currentData, $fieldName, $state);
        $block->setTranslatedData($updatedData);
        $block->save();
        return $block;
    }

    /**
     * Update nested repeater state for a block.
     *
     * @param ContentBlock $block The block containing the nested repeater
     * @param string $fieldName The parent repeater field name
     * @param int $index The index of the parent repeater item
     * @param string $nestedFieldName The nested repeater field name
     * @param array<string, mixed> $state The new nested repeater state
     * @return ContentBlock The updated block
     */
    public function updateNestedRepeaterState(
        ContentBlock $block,
        string $fieldName,
        int $index,
        string $nestedFieldName,
        array $state
    ): ContentBlock {
        $currentData = $block->getTranslatedData();
        $nestedPath = $fieldName . '.' . $index . '.' . $nestedFieldName;
        $updatedData = $this->updateRepeaterStateInArray($currentData, $nestedPath, $state);
        $block->setTranslatedData($updatedData);
        $block->save();
        return $block;
    }

    /**
     * Load block data with translatable content.
     *
     * @param ContentBlock $block The block to load data for
     * @param string $locale The locale to load data for
     * @return array<string, mixed> The block data with translations
     */
    public function loadBlockDataWithTranslatableContent(ContentBlock $block, string $locale = 'en'): array
    {
        return $this->loadBlockData($block, $locale);
    }

    /**
     * Save block data.
     *
     * @param ContentBlock $block The block to save
     * @param array<string, mixed> $data The data to save
     * @param string $locale The locale to save data for
     * @return ContentBlock The saved block
     */
    public function saveBlockData(ContentBlock $block, array $data, string $locale = 'en'): ContentBlock
    {
        $block->setTranslatedData($data, $locale);
        $block->save();
        return $block;
    }

    /**
     * Get available block types.
     *
     * @return Collection<string> Collection of available block types
     */
    public function getAvailableBlockTypes(): Collection
    {
        return collect($this->blockManager->getAvailableTypes());
    }

    /**
     * Create a new block.
     *
     * @param Page $page The page to add the block to
     * @param string $type The block type
     * @param array<string, mixed> $data The initial block data
     * @param int $order The block order
     * @return ContentBlock The created block
     */
    public function createBlock(Page $page, string $type, array $data = [], int $order = 0): ContentBlock
    {
        $block = new ContentBlock();
        $block->page_id = $page->id;
        $block->type = $type;
        $block->data = $data;
        $block->order = $order;
        $block->save();
        return $block;
    }

    /**
     * Delete a block.
     *
     * @param ContentBlock $block The block to delete
     * @return bool True if the block was deleted successfully
     */
    public function deleteBlock(ContentBlock $block): bool
    {
        return $block->delete();
    }

    /**
     * Reorder blocks on a page.
     *
     * @param Page $page The page containing the blocks
     * @param array<int> $blockIds The ordered array of block IDs
     * @return bool True if the blocks were reordered successfully
     */
    public function reorderBlocks(Page $page, array $blockIds): bool
    {
        foreach ($blockIds as $index => $blockId) {
            ContentBlock::where('id', $blockId)
                ->where('page_id', $page->id)
                ->update(['order' => $index]);
        }
        return true;
    }

    /**
     * Duplicate a block.
     *
     * @param ContentBlock $block The block to duplicate
     * @return ContentBlock The duplicated block
     */
    public function duplicateBlock(ContentBlock $block): ContentBlock
    {
        $newBlock = $block->replicate();
        $newBlock->order = $block->order + 1;
        $newBlock->save();
        return $newBlock;
    }

    /**
     * Get block validation rules.
     *
     * @param ContentBlock $block The block to get validation rules for
     * @return array<string, mixed> The validation rules
     */
    public function getBlockValidationRules(ContentBlock $block): array
    {
        $blockClass = $this->getBlockClass($block);
        return $blockClass instanceof \App\Blocks\Block ? $blockClass->getValidationRules() : [];
    }

    /**
     * Validate block data.
     *
     * @param ContentBlock $block The block to validate
     * @param array<string, mixed> $data The data to validate
     * @return array<string, string> Array of validation errors (empty if valid)
     */
    public function validateBlockData(ContentBlock $block, array $data): array
    {
        $rules = $this->getBlockValidationRules($block);
        $validator = validator($data, $rules);
        
        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        
        return [];
    }
}
