<?php

declare(strict_types=1);

namespace App\Services;

use App\Blocks\Block;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Service for managing content blocks in the page builder system.
 *
 * This service is responsible for discovering, loading, and providing
 * access to all available content blocks. It automatically scans the
 * Blocks directory and provides methods to find and retrieve blocks
 * by their type.
 */
class BlockManager
{
    /**
     * Cached collection of available blocks.
     *
     * @var Collection<Block>|null
     */
    protected ?Collection $blocks = null;

    /**
     * Get all available content blocks.
     *
     * This method scans the Blocks directory, loads all block classes,
     * and returns them as a collection. The result is cached for
     * performance on subsequent calls.
     *
     * @return Collection<Block> Collection of available blocks keyed by type
     */
    public function getAvailableBlocks(): Collection
    {
        if ($this->blocks instanceof Collection) {
            return $this->blocks;
        }

        $this->blocks = collect(File::allFiles(app_path('Blocks')))
            ->map(function ($file) {
                $class = 'App\\Blocks\\'.$file->getBasename('.php');
                if (! class_exists($class)) {
                    return null;
                }
                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Block::class)) {
                    return null;
                }

                return app($class);
            })
            ->filter()
            ->keyBy(fn (Block $block): string => $block->getType());

        return $this->blocks;
    }

    /**
     * Find a specific block by its type.
     *
     * @param  string  $type  The block type identifier
     * @return Block|null The block instance or null if not found
     */
    public function find(string $type): ?Block
    {
        return $this->getAvailableBlocks()->get($type);
    }
}
