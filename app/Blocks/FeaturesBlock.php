<?php

namespace App\Blocks;

class FeaturesBlock extends Block
{
    public function getName(): string
    {
        return 'Features';
    }

    public function getDescription(): string
    {
        return 'Display a list of features with icons, titles, and descriptions.';
    }

    public function getCategory(): string
    {
        return 'content';
    }

    public function getTags(): array
    {
        return ['features', 'benefits', 'highlights', 'icons'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'star';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Why Choose Us',
            'subheading' => 'Discover what makes us different and why you should choose our services.',
            'layout' => 'grid',
            'columns' => 3,
            'features' => [
                [
                    'icon' => 'shield-check',
                    'title' => 'Secure & Reliable',
                    'description' => 'Your data is protected with enterprise-grade security measures.',
                    'color' => 'blue',
                ],
                [
                    'icon' => 'bolt',
                    'title' => 'Lightning Fast',
                    'description' => 'Experience blazing fast performance with our optimized platform.',
                    'color' => 'yellow',
                ],
                [
                    'icon' => 'heart',
                    'title' => 'Customer First',
                    'description' => 'We prioritize your success with dedicated support and care.',
                    'color' => 'red',
                ],
            ],
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['heading', 'subheading', 'features'];
    }

    public function validationRules(): array
    {
        return [
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'layout' => 'required|string|in:grid,list',
            'columns' => 'required|integer|min:1|max:4',
            'features' => 'required|array|min:1',
            'features.*.icon' => 'required|string|max:255',
            'features.*.title' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:500',
            'features.*.color' => 'required|string|in:blue,green,yellow,red,purple,indigo,pink,orange',
        ];
    }
} 