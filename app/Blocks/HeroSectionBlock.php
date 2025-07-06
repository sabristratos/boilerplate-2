<?php

declare(strict_types=1);

namespace App\Blocks;

/**
 * Hero Section Block for creating eye-catching hero sections.
 *
 * This block creates prominent hero sections typically used at the top
 * of landing pages. It supports background images, overlay effects,
 * customizable text alignment, and multiple call-to-action buttons.
 */
class HeroSectionBlock extends Block
{
    /**
     * Get the human-readable name of the block.
     *
     * @return string The display name of the block
     */
    public function getName(): string
    {
        return 'Hero Section';
    }

    /**
     * Get a description of what this block does.
     *
     * @return string A description of the block's functionality
     */
    public function getDescription(): string
    {
        return 'Create an eye-catching hero section with heading, subheading, and call-to-action buttons.';
    }

    /**
     * Get the category this block belongs to.
     *
     * @return string The category name
     */
    public function getCategory(): string
    {
        return 'layout';
    }

    /**
     * Get tags associated with this block.
     *
     * @return array<string> Array of tag strings
     */
    public function getTags(): array
    {
        return ['hero', 'cta', 'landing', 'banner'];
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
        return 'photo';
    }

    /**
     * Get the default data for this block.
     *
     * @return array<string, mixed> Default data array
     */
    public function getDefaultData(): array
    {
        return [
            'overline' => 'Welcome',
            'heading' => 'Build Something Amazing',
            'subheading' => 'Create stunning websites with our powerful page builder and beautiful components.',
            'background_image' => '',
            'background_overlay' => 70,
            'text_alignment' => 'center',
            'content_width' => 'max-w-4xl',
            'padding' => 'py-24',
            'buttons' => [
                [
                    'text' => 'Get Started',
                    'url' => '#',
                    'variant' => 'primary',
                ],
                [
                    'text' => 'Learn More',
                    'url' => '#',
                    'variant' => 'secondary',
                ],
            ],
        ];
    }

    /**
     * Get the fields that should be translatable.
     *
     * @return array<string> Array of field names that are translatable
     */
    public function getTranslatableFields(): array
    {
        return ['overline', 'heading', 'subheading', 'background_image', 'buttons'];
    }

    /**
     * Get validation rules for the block's data fields.
     *
     * @return array<string, string> Laravel validation rules
     */
    public function validationRules(): array
    {
        return [
            'overline' => 'nullable|string|max:255',
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'background_image' => 'nullable|url',
            'background_overlay' => 'integer|min:0|max:100',
            'text_alignment' => 'string|in:left,center,right',
            'content_width' => 'string|in:max-w-2xl,max-w-3xl,max-w-4xl,max-w-5xl,max-w-6xl,max-w-7xl',
            'padding' => 'string|in:py-16,py-20,py-24,py-32,py-40,py-48',
            'buttons' => 'nullable|array',
            'buttons.*.text' => 'required|string|max:255',
            'buttons.*.variant' => 'required|string|in:primary,secondary,ghost',
            'buttons.*.url' => 'required|string',
        ];
    }
}
