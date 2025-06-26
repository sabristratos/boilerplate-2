<?php

namespace App\Blocks;

class GlobalBlockInstanceBlock extends Block
{
    public function getName(): string
    {
        return 'Global Block';
    }

    public function getIcon(): string
    {
        return 'globe-alt'; // A suitable icon from Heroicons
    }

    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._global-block-instance';
    }

    public function getFrontendView(): string
    {
        return 'frontend.blocks._global-block-instance';
    }

    public function getDefaultData(): array
    {
        return [
            'heading' => 'Global Heading',
        ];
    }

    public function getTranslatableFields(): array
    {
        return ['heading'];
    }

    public function validationRules(): array
    {
        return [
            // The only data we need to validate is the ID of the master block.
            'global_block_id' => 'required|exists:global_blocks,id',
        ];
    }
} 