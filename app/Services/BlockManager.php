<?php

namespace App\Services;

use App\Blocks\Block;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class BlockManager
{
    protected ?Collection $blocks = null;

    public function getAvailableBlocks(): Collection
    {
        if ($this->blocks) {
            return $this->blocks;
        }

        $this->blocks = collect(File::allFiles(app_path('Blocks')))
            ->map(function ($file) {
                $class = 'App\\Blocks\\' . $file->getBasename('.php');
                if (!class_exists($class)) {
                    return null;
                }
                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract() || !$reflection->isSubclassOf(Block::class)) {
                    return null;
                }
                return app($class);
            })
            ->filter()
            ->keyBy(fn (Block $block) => $block->getType());

        return $this->blocks;
    }

    public function find(string $type): ?Block
    {
        return $this->getAvailableBlocks()->get($type);
    }
} 