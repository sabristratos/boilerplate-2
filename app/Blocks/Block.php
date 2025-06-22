<?php

namespace App\Blocks;

use Illuminate\Support\Str;

abstract class Block
{
    abstract public function getName(): string;

    abstract public function getAdminView(): string;

    abstract public function getFrontendView(): string;

    public function getType(): string
    {
        return Str::kebab(Str::beforeLast(class_basename($this), 'Block'));
    }

    public function getIcon(): string
    {
        return 'code-bracket';
    }

    public function getDefaultData(): array
    {
        return [];
    }
} 