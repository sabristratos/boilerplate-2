<?php

declare(strict_types=1);

namespace App\Blocks;

/**
 * Content Area Block for rich text content with formatting options.
 *
 * This block provides a rich text editor for creating formatted content
 * with optional form integration. It supports basic text formatting,
 * links, and can optionally display a form above, below, or inline
 * with the content.
 */
class ContentAreaBlock extends Block
{
    /**
     * Get the human-readable name of the block.
     *
     * @return string The display name of the block
     */
    public function getName(): string
    {
        return 'Content Area';
    }

    /**
     * Get a description of what this block does.
     *
     * @return string A description of the block's functionality
     */
    public function getDescription(): string
    {
        return 'Add rich text content with formatting options.';
    }

    /**
     * Get the category this block belongs to.
     *
     * @return string The category name
     */
    public function getCategory(): string
    {
        return 'content';
    }

    /**
     * Get tags associated with this block.
     *
     * @return array<string> Array of tag strings
     */
    public function getTags(): array
    {
        return ['text', 'content', 'rich-text', 'editor'];
    }

    /**
     * Get the complexity level of this block.
     *
     * @return string The complexity level
     */
    public function getComplexity(): string
    {
        return 'basic';
    }

    /**
     * Get the icon name for this block.
     *
     * @return string The icon name
     */
    public function getIcon(): string
    {
        return 'document-text';
    }

    /**
     * Get the default data for this block.
     *
     * @return array<string, mixed> Default data array
     */
    public function getDefaultData(): array
    {
        return [
            'content' => 'This is a content area.',
            'form_id' => null,
            'show_form' => false,
            'form_position' => 'bottom', // 'top', 'bottom', 'inline'
        ];
    }

    /**
     * Get the fields that should be translatable.
     *
     * @return array<string> Array of field names that are translatable
     */
    public function getTranslatableFields(): array
    {
        return ['content'];
    }

    /**
     * Get validation rules for the block's data fields.
     *
     * @return array<string, string> Laravel validation rules
     */
    public function validationRules(): array
    {
        return [
            'content' => 'required|string',
            'form_id' => 'nullable|integer|exists:forms,id',
            'show_form' => 'boolean',
            'form_position' => 'string|in:top,bottom,inline',
        ];
    }
}
