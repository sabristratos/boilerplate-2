<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Interface for block editor service operations.
 *
 * This interface defines the contract for content block management,
 * including block operations, validation, and editor functionality.
 */
interface BlockEditorServiceInterface
{
    /**
     * Get a block by its ID.
     *
     * @param int $blockId The block ID
     * @return ContentBlock|null The block or null if not found
     */
    public function getBlockById(int $blockId): ?ContentBlock;

    /**
     * Check if a block is valid.
     *
     * @param ContentBlock $block The block to validate
     * @return bool True if the block is valid
     */
    public function isBlockValid(ContentBlock $block): bool;

    /**
     * Get block visibility status.
     *
     * @param ContentBlock $block The block to check
     * @return bool True if the block is visible
     */
    public function getBlockVisibility(ContentBlock $block): bool;

    /**
     * Update block visibility.
     *
     * @param ContentBlock $block The block to update
     * @param bool $visible The visibility status
     * @return ContentBlock The updated block
     */
    public function updateBlockVisibility(ContentBlock $block, bool $visible): ContentBlock;

    /**
     * Update repeater state for a block.
     *
     * @param ContentBlock $block The block containing the repeater
     * @param string $fieldName The repeater field name
     * @param array<string, mixed> $state The new repeater state
     * @return ContentBlock The updated block
     */
    public function updateRepeaterState(ContentBlock $block, string $fieldName, array $state): ContentBlock;

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
    ): ContentBlock;

    /**
     * Load block data for editing.
     *
     * @param ContentBlock $block The block to load data for
     * @param string $locale The locale to load data for
     * @return array<string, mixed> The block data
     */
    public function loadBlockData(ContentBlock $block, string $locale = 'en'): array;

    /**
     * Load block data with translatable content.
     *
     * @param ContentBlock $block The block to load data for
     * @param string $locale The locale to load data for
     * @return array<string, mixed> The block data with translations
     */
    public function loadBlockDataWithTranslatableContent(ContentBlock $block, string $locale = 'en'): array;

    /**
     * Save block data.
     *
     * @param ContentBlock $block The block to save
     * @param array<string, mixed> $data The data to save
     * @param string $locale The locale to save data for
     * @return ContentBlock The saved block
     */
    public function saveBlockData(ContentBlock $block, array $data, string $locale = 'en'): ContentBlock;

    /**
     * Get available block types.
     *
     * @return Collection<string> Collection of available block types
     */
    public function getAvailableBlockTypes(): Collection;

    /**
     * Create a new block.
     *
     * @param Page $page The page to add the block to
     * @param string $type The block type
     * @param array<string, mixed> $data The initial block data
     * @param int $order The block order
     * @return ContentBlock The created block
     */
    public function createBlock(Page $page, string $type, array $data = [], int $order = 0): ContentBlock;

    /**
     * Delete a block.
     *
     * @param ContentBlock $block The block to delete
     * @return bool True if the block was deleted successfully
     */
    public function deleteBlock(ContentBlock $block): bool;

    /**
     * Reorder blocks on a page.
     *
     * @param Page $page The page containing the blocks
     * @param array<int> $blockIds The ordered array of block IDs
     * @return bool True if the blocks were reordered successfully
     */
    public function reorderBlocks(Page $page, array $blockIds): bool;

    /**
     * Duplicate a block.
     *
     * @param ContentBlock $block The block to duplicate
     * @return ContentBlock The duplicated block
     */
    public function duplicateBlock(ContentBlock $block): ContentBlock;

    /**
     * Get block validation rules.
     *
     * @param ContentBlock $block The block to get validation rules for
     * @return array<string, mixed> The validation rules
     */
    public function getBlockValidationRules(ContentBlock $block): array;

    /**
     * Validate block data.
     *
     * @param ContentBlock $block The block to validate
     * @param array<string, mixed> $data The data to validate
     * @return array<string, string> Array of validation errors (empty if valid)
     */
    public function validateBlockData(ContentBlock $block, array $data): array;
} 