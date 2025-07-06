<?php

namespace App\Blocks;

class TestimonialsBlock extends Block
{
    public function getName(): string
    {
        return 'Testimonials';
    }

    public function getDescription(): string
    {
        return 'Display customer testimonials with avatars, names, and quotes.';
    }

    public function getCategory(): string
    {
        return 'social';
    }

    public function getTags(): array
    {
        return ['testimonials', 'reviews', 'quotes', 'social-proof'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'chat-bubble-left-right';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'What Our Customers Say',
            'subheading' => 'Don\'t just take our word for it. Here\'s what our customers have to say about their experience.',
            'layout' => 'grid',
            'columns' => 3,
            'show_avatars' => true,
            'show_ratings' => true,
            'testimonials' => [
                [
                    'name' => 'Sarah Johnson',
                    'title' => 'CEO, TechStart',
                    'quote' => 'This platform has transformed how we handle our business operations. The ease of use and powerful features make it a game-changer.',
                    'rating' => 5,
                    'avatar' => '',
                ],
                [
                    'name' => 'Michael Chen',
                    'title' => 'Marketing Director',
                    'quote' => 'The customer support is exceptional and the platform delivers exactly what it promises. Highly recommended!',
                    'rating' => 5,
                    'avatar' => '',
                ],
                [
                    'name' => 'Emily Rodriguez',
                    'title' => 'Product Manager',
                    'quote' => 'We\'ve seen a significant improvement in our workflow efficiency since implementing this solution.',
                    'rating' => 5,
                    'avatar' => '',
                ],
            ],
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['heading', 'subheading', 'testimonials'];
    }

    public function validationRules(): array
    {
        return [
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'layout' => 'required|string|in:grid,carousel',
            'columns' => 'required|integer|min:1|max:4',
            'show_avatars' => 'boolean',
            'show_ratings' => 'boolean',
            'testimonials' => 'required|array|min:1',
            'testimonials.*.name' => 'required|string|max:255',
            'testimonials.*.title' => 'nullable|string|max:255',
            'testimonials.*.quote' => 'required|string|max:1000',
            'testimonials.*.rating' => 'required|integer|min:1|max:5',
            'testimonials.*.avatar' => 'nullable|string',
        ];
    }
} 