<?php

namespace App\Blocks;

class HeroSectionBlock extends Block
{
    public function getName(): string
    {
        return 'Hero Section';
    }

    public function getIcon(): string
    {
        return 'photo';
    }

    public function getDefaultData(): array
    {
        return [
            'overline' => 'Overline',
            'heading' => 'Heading',
            'subheading' => 'Subheading',
            'buttons' => [],
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['overline', 'heading', 'subheading', 'buttons.*.text'];
    }

    public function validationRules(): array
    {
        return [
            'overline' => 'nullable|string|max:255',
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'buttons' => 'nullable|array',
            'buttons.*.text' => 'required|string|max:255',
            'buttons.*.variant' => 'required|string|in:primary,secondary,ghost',
            'buttons.*.url' => 'required|url',
        ];
    }
}
