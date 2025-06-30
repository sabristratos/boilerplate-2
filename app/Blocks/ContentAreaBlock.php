<?php

namespace App\Blocks;

class ContentAreaBlock extends Block
{
    public function getName(): string
    {
        return 'Content Area';
    }

    public function getIcon(): string
    {
        return 'document-text';
    }

    public function getDefaultData(): array
    {
        return [
            'content' => 'This is a content area.',
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
