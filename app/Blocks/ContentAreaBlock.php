<?php

namespace App\Blocks;

class ContentAreaBlock extends Block
{
    public function getName(): string
    {
        return 'Content Area';
    }

    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._content-area';
    }

    public function getFrontendView(): string
    {
        return 'frontend.blocks._content-area';
    }

    public function getDefaultData(): array
    {
        return [
            'content' => __('blocks.content_area.default_content'),
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['content'];
    }

    public function validationRules(): array
    {
        return [
            'content' => 'required|string',
        ];
    }
} 