<?php

declare(strict_types=1);

namespace App\Blocks;

use Illuminate\Support\Str;

/**
 * Abstract base class for all content blocks in the page builder system.
 *
 * This class provides the foundation for creating custom content blocks
 * that can be used in the page editor. Each block type must extend this
 * class and implement the required methods.
 */
abstract class Block
{
    /**
     * Get the human-readable name of the block.
     *
     * This name will be displayed in the block library and editor.
     *
     * @return string The display name of the block
     */
    abstract public function getName(): string;

    /**
     * Get a description of what this block does.
     *
     * This description helps users understand the purpose of the block
     * when browsing the block library.
     *
     * @return string A description of the block's functionality
     */
    public function getDescription(): string
    {
        return 'A content block for building pages.';
    }

    /**
     * Get the category this block belongs to.
     *
     * Categories help organize blocks in the library for easier discovery.
     * Common categories include: 'content', 'layout', 'media', 'forms'.
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
     * Tags help with searching and filtering blocks in the library.
     *
     * @return array<string> Array of tag strings
     */
    public function getTags(): array
    {
        return [];
    }

    /**
     * Get the complexity level of this block.
     *
     * Complexity levels help users understand how advanced a block is.
     * Common levels: 'basic', 'intermediate', 'advanced'.
     *
     * @return string The complexity level
     */
    public function getComplexity(): string
    {
        return 'basic';
    }

    /**
     * Get the default settings for this block.
     *
     * Settings are block-specific configuration options that affect
     * the block's behavior or appearance.
     *
     * @return array<string, mixed> Default settings array
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * Get validation rules for block settings.
     *
     * These rules are used to validate the settings when the block
     * is saved or updated.
     *
     * @return array<string, string> Laravel validation rules
     */
    public function getSettingsValidationRules(): array
    {
        return [];
    }

    /**
     * Get the unique type identifier for this block.
     *
     * The type is automatically generated from the class name by
     * converting it to kebab-case and removing the 'Block' suffix.
     *
     * @return string The block type identifier
     */
    public function getType(): string
    {
        return Str::kebab(str_replace('Block', '', class_basename(static::class)));
    }

    /**
     * Get the icon name for this block.
     *
     * The icon is displayed in the block library and editor.
     * Uses Heroicon names.
     *
     * @return string The icon name
     */
    public function getIcon(): string
    {
        return 'code-bracket';
    }

    /**
     * Get the path to the admin view for editing this block.
     *
     * The admin view contains the form fields for editing the block's
     * content and settings.
     *
     * @return string The view path
     */
    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._'.Str::kebab($this->getType());
    }

    /**
     * Get the path to the frontend view for rendering this block.
     *
     * The frontend view is used to render the block on the live website.
     *
     * @return string The view path
     */
    public function getFrontendView(): string
    {
        return 'frontend.blocks._'.Str::kebab($this->getType());
    }

    /**
     * Get the default data for this block.
     *
     * This data is used when a new instance of this block is created.
     *
     * @return array<string, mixed> Default data array
     */
    public function getDefaultData(): array
    {
        return [];
    }

    /**
     * Get the fields that should be translatable.
     *
     * These field names will be stored as translatable content
     * and can have different values for different locales.
     *
     * @return array<string> Array of field names that are translatable
     */
    public function getTranslatableFields(): array
    {
        return [];
    }

    /**
     * Get validation rules for the block's data fields.
     *
     * These rules are used to validate the block's content when
     * it is saved or updated.
     *
     * @return array<string, string> Laravel validation rules
     */
    public function validationRules(): array
    {
        return [];
    }
}
