<?php

namespace App\Blocks;

class HeroSectionBlock extends Block
{
    public function getName(): string
    {
        return 'Hero Section';
    }

    public function getDescription(): string
    {
        return 'Create an eye-catching hero section with heading, subheading, and call-to-action buttons.';
    }

    public function getCategory(): string
    {
        return 'layout';
    }

    public function getTags(): array
    {
        return ['hero', 'cta', 'landing', 'banner'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'photo';
    }

    public function getDefaultData(): array
    {
        return [
            'overline' => 'Overline',
            'heading' => 'Heading',
            'subheading' => 'Subheading',
            'background_image' => '',
            'buttons' => [
                [
                    'text' => 'Click me',
                    'url' => '#',
                    'variant' => 'primary',
                ],
            ],
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['overline', 'heading', 'subheading', 'background_image', 'buttons'];
    }

    public function validationRules(): array
    {
        return [
            'overline' => 'nullable|string|max:255',
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'background_image' => 'nullable|url',
            'buttons' => 'nullable|array',
            'buttons.*.text' => 'required|string|max:255',
            'buttons.*.variant' => 'required|string|in:primary,secondary,ghost',
            'buttons.*.url' => 'required|string',
        ];
    }
}
