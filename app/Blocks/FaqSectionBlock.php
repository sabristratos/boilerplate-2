<?php

namespace App\Blocks;

class FaqSectionBlock extends Block
{
    public function getName(): string
    {
        return 'FAQ Section';
    }

    public function getDescription(): string
    {
        return 'Display frequently asked questions in an organized accordion or list format.';
    }

    public function getCategory(): string
    {
        return 'interactive';
    }

    public function getTags(): array
    {
        return ['faq', 'accordion', 'help', 'support'];
    }

    public function getComplexity(): string
    {
        return 'intermediate';
    }

    public function getIcon(): string
    {
        return 'question-mark-circle';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Frequently Asked Questions',
            'subheading' => 'Find answers to common questions below.',
            'faqs' => [
                [
                    'question' => 'How do I get started?',
                    'answer' => 'Getting started is easy! Simply sign up for an account and follow our step-by-step guide.',
                ],
                [
                    'question' => 'What features are included?',
                    'answer' => 'Our platform includes a powerful page builder, beautiful components, and comprehensive management tools.',
                ],
            ],
            'style' => 'accordion', // 'accordion', 'list'
            'expand_first' => false,
            'text_alignment' => 'center',
            'max_width' => 'max-w-4xl',
            'show_icons' => true,
            'background_color' => 'bg-white',
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['heading', 'subheading', 'faqs'];
    }

    public function validationRules(): array
    {
        return [
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:500',
            'faqs' => 'required|array|min:1',
            'faqs.*.question' => 'required|string|max:255',
            'faqs.*.answer' => 'required|string',
            'style' => 'string|in:accordion,list',
            'expand_first' => 'boolean',
            'text_alignment' => 'string|in:left,center,right',
            'max_width' => 'string|in:max-w-2xl,max-w-3xl,max-w-4xl,max-w-5xl,max-w-6xl,max-w-7xl',
            'show_icons' => 'boolean',
            'background_color' => 'string|in:bg-white,bg-zinc-50,bg-blue-50,bg-gray-50,transparent',
        ];
    }
}
