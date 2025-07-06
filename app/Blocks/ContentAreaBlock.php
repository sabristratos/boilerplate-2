<?php

namespace App\Blocks;

class ContentAreaBlock extends Block
{
    public function getName(): string
    {
        return 'Content Area';
    }

    public function getDescription(): string
    {
        return 'Add rich text content with formatting options.';
    }

    public function getCategory(): string
    {
        return 'content';
    }

    public function getTags(): array
    {
        return ['text', 'content', 'rich-text', 'editor'];
    }

    public function getComplexity(): string
    {
        return 'basic';
    }

    public function getIcon(): string
    {
        return 'document-text';
    }

    public function getDefaultData(): array
    {
        return [
            'content' => 'This is a content area.',
            'form_id' => null,
            'show_form' => false,
            'form_position' => 'bottom', // 'top', 'bottom', 'inline'
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
            'form_id' => 'nullable|integer|exists:forms,id',
            'show_form' => 'boolean',
            'form_position' => 'string|in:top,bottom,inline',
        ];
    }
}
