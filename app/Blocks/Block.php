<?php

namespace App\Blocks;

use Illuminate\Support\Str;

abstract class Block
{
    abstract public function getName(): string;

    public function getType(): string
    {
        return Str::kebab(str_replace('Block', '', class_basename(static::class)));
    }

    public function getIcon(): string
    {
        return 'code-bracket';
    }

    public function getAdminView(): string
    {
        return 'livewire.admin.block-forms._'.Str::kebab($this->getType());
    }

    public function getFrontendView(): string
    {
        return 'frontend.blocks._'.Str::kebab($this->getType());
    }

    public function getDefaultData(): array
    {
        return [];
    }

    public function getTranslatableFields(): array
    {
        return [];
    }

    public function validationRules(): array
    {
        return [];
    }
}
