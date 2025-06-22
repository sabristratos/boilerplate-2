<?php

namespace App\Blocks;

class HeroSectionBlock extends Block
{
    public function getName(): string
    {
        return 'Hero Section';
    }

    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._hero-section';
    }

    public function getFrontendView(): string
    {
        return 'frontend.blocks._hero-section';
    }

    public function getIcon(): string
    {
        return 'layout-grid';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'New Hero Heading',
            'subheading' => 'Subheading text goes here.',
        ];
    }
} 