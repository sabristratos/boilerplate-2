<?php

namespace App\Blocks;

class FaqSectionBlock extends Block
{
    public function getName(): string
    {
        return 'FAQ Section';
    }

    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._faq-section';
    }

    public function getFrontendView(): string
    {
        return 'frontend.blocks._faq-section';
    }

    public function getDefaultData(): array
    {
        return [
            'faqs' => [
                ['question' => 'First Question?', 'answer' => 'First answer.'],
            ],
        ];
    }
} 