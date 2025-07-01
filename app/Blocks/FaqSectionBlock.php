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
        return 'Display frequently asked questions in an organized accordion format.';
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
                    'question' => 'What is this FAQ about?',
                    'answer' => 'This is a sample answer to demonstrate the FAQ functionality.',
                ],
            ],
            'style' => 'accordion', // 'accordion', 'list'
            'expand_first' => false,
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
        ];
    }
}
