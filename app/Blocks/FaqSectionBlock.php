<?php

namespace App\Blocks;

class FaqSectionBlock extends Block
{
    public function getName(): string
    {
        return 'FAQ Section';
    }

    public function getIcon(): string
    {
        return 'question-mark-circle';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Frequently Asked Questions',
            'questions' => [],
        ];
    }

    public function validationRules(): array
    {
        return [
            'faqs' => 'required|array',
            'faqs.*.question' => 'required|string|max:255',
            'faqs.*.answer' => 'required|string',
        ];
    }
}
