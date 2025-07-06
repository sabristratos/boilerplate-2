<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Content\CreateContentBlockAction;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use Livewire\Component;

/**
 * Block Library component for creating and managing content blocks.
 */
class BlockLibrary extends Component
{
    use WithToastNotifications;

    /**
     * The page being edited.
     */
    public Page $page;

    /**
     * Available locales for the application.
     *
     * @var array<string, string>
     */
    public array $availableLocales = [];

    // Block library filtering
    /**
     * Search term for filtering blocks.
     */
    public string $blockSearch = '';

    /**
     * Selected category for filtering blocks.
     */
    public string $selectedCategory = '';

    /**
     * Selected complexity level for filtering blocks.
     */
    public string $selectedComplexity = '';

    /**
     * Block manager service instance.
     */
    protected BlockManager $blockManager;

    /**
     * Mount the component with the page to edit.
     */
    public function mount(Page $page, array $availableLocales): void
    {
        $this->page = $page;
        $this->availableLocales = $availableLocales;
    }

    /**
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    /**
     * Get filtered blocks based on search and filter criteria.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFilteredBlocksProperty()
    {
        $blocks = $this->blockManager->getAvailableBlocks();

        // Filter by search
        if (! empty($this->blockSearch)) {
            $blocks = $blocks->filter(function ($block) {
                return str_contains(strtolower($block->getName()), strtolower($this->blockSearch)) ||
                       str_contains(strtolower($block->getDescription()), strtolower($this->blockSearch)) ||
                       collect($block->getTags())->contains(function ($tag) {
                           return str_contains(strtolower($tag), strtolower($this->blockSearch));
                       });
            });
        }

        // Filter by category
        if (! empty($this->selectedCategory)) {
            $blocks = $blocks->filter(function ($block) {
                return $block->getCategory() === $this->selectedCategory;
            });
        }

        // Filter by complexity
        if (! empty($this->selectedComplexity)) {
            $blocks = $blocks->filter(function ($block) {
                return $block->getComplexity() === $this->selectedComplexity;
            });
        }

        return $blocks;
    }

    /**
     * Get available categories for filtering.
     *
     * @return array<string>
     */
    public function getAvailableCategoriesProperty()
    {
        return $this->blockManager->getAvailableBlocks()
            ->pluck('category')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Get available complexity levels for filtering.
     *
     * @return array<string>
     */
    public function getAvailableComplexitiesProperty()
    {
        return $this->blockManager->getAvailableBlocks()
            ->pluck('complexity')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Create a new block of the specified type.
     */
    public function createBlock(string $type): void
    {
        try {
            $createContentBlockAction = app(CreateContentBlockAction::class);
            $block = $createContentBlockAction->execute($this->page, $type, $this->availableLocales);

            $this->dispatch('block-created', [
                'blockId' => $block->id,
                'blockType' => $type
            ]);

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_creation_failed_text'),
                __('messages.page_manager.block_creation_failed_title')
            );
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.block-library', [
            'blockManager' => $this->blockManager
        ]);
    }
} 