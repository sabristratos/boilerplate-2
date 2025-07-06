<?php

declare(strict_types=1);

namespace App\Blocks;

class CallToActionBlock extends Block
{
    public function getName(): string
    {
        return 'Call to Action';
    }

    public function getDescription(): string
    {
        return 'Create compelling call-to-action sections with buttons and engaging content.';
    }

    public function getCategory(): string
    {
        return 'conversion';
    }

    public function getTags(): array
    {
        return ['cta', 'conversion', 'buttons', 'action'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'arrow-right';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Ready to Get Started?',
            'subheading' => 'Join thousands of satisfied customers who have already transformed their business with our platform.',
            'background_color' => 'blue',
            'text_alignment' => 'center',
            'buttons' => [
                [
                    'text' => 'Get Started Now',
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
        return ['heading', 'subheading', 'buttons'];
    }

    public function validationRules(): array
    {
        return [
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'background_color' => 'required|string|in:blue,green,purple,orange,red,gray',
            'text_alignment' => 'required|string|in:left,center,right',
            'buttons' => 'nullable|array',
            'buttons.*.text' => 'required|string|max:255',
            'buttons.*.variant' => 'required|string|in:primary,secondary,ghost',
            'buttons.*.url' => 'required|string',
        ];
    }
}
