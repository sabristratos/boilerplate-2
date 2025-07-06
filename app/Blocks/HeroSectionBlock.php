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

    public function getTranslatableFields(): array
    {
        return ['overline', 'heading', 'subheading', 'background_image', 'buttons'];
    }

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
