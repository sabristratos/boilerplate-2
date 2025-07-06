<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Content\CreateContentBlockAction;
use App\Actions\Content\DeleteContentBlockAction;
use App\Actions\Content\SaveDraftContentBlockAction;
use App\Actions\Content\SaveDraftPageDetailsAction;
use App\Actions\Content\UpdateBlockOrderAction;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Page Manager Livewire component for editing pages and their content blocks.
 *
 * This component provides the main interface for managing page content,
 * including adding, editing, reordering, and deleting content blocks.
 * It supports multi-locale content editing and draft/published states.
 */
class PageManager extends Component
{
    use WithConfirmationModal, WithFileUploads, WithToastNotifications;

    /**
     * The page being edited.
     */
    public Page $page;

    // Page-level properties (draft versions)
    /**
     * Page title translations.
     *
     * @var array<string, string>
     */
    public array $title = [];

    /**
     * Page slug.
     */
    public ?string $slug = '';

    /**
     * Page meta title translations.
     *
     * @var array<string, string>
     */
    public array $meta_title = [];

    /**
     * Page meta description translations.
     *
     * @var array<string, string>
     */
    public array $meta_description = [];

    /**
     * Whether the page should be indexed by search engines.
     */
    public bool $no_index = false;

    // Locale management
    /**
     * Currently active locale for editing.
     */
    public string $activeLocale;

    /**
     * Available locales for the application.
     *
     * @var array<string, string>
     */
    public array $availableLocales = [];

    /**
     * Locale to switch to.
     */
    public ?string $switchLocale = null;

    // Block editing state
    /**
     * ID of the block currently being edited.
     */
    public ?int $editingBlockId = null;

    /**
     * State data for the block being edited.
     *
     * @var array<string, mixed>
     */
    public array $editingBlockState = [];

    /**
     * Whether the block being edited is visible.
     */
    public bool $editingBlockVisible = true;

    /**
     * Image upload for the block being edited.
     *
     * @var mixed
     */
    public $editingBlockImageUpload;

    // UI state
    /**
     * Currently active tab in the editor.
     */
    public string $tab = 'settings';

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
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    /**
     * Mount the component with the page to edit.
     */
    public function mount(Page $page): void
    {
        $this->authorize('update', $page);

        $this->page = $page;
        $this->initializeLocale();
        $this->loadPageTranslations();
    }

    /**
     * Initialize locale settings for the component.
     */
    protected function initializeLocale(): void
    {
        $this->availableLocales = $this->getAvailableLocales();
        $requestedLocale = request()->query('locale', config('app.fallback_locale'));

        // Validate locale format (2-3 character language code)
        if (! preg_match('/^[a-z]{2,3}$/', $requestedLocale)) {
            $requestedLocale = config('app.fallback_locale');
        }

        $this->activeLocale = array_key_exists($requestedLocale, $this->availableLocales)
            ? $requestedLocale
            : config('app.fallback_locale');

        $this->switchLocale = $this->activeLocale;
        app()->setLocale($this->activeLocale);
    }

    /**
     * Load page translations into the component state.
     */
    protected function loadPageTranslations(): void
    {
        // Load draft data if available, otherwise fall back to published data
        $draftTitle = $this->page->getTranslations('draft_title');
        $this->title = ! empty($draftTitle) ? $draftTitle : $this->page->getTranslations('title');

        $this->slug = $this->page->draft_slug ?? $this->page->slug;

        $draftMetaTitle = $this->page->getTranslations('draft_meta_title');
        $this->meta_title = ! empty($draftMetaTitle) ? $draftMetaTitle : $this->page->getTranslations('meta_title');

        $draftMetaDescription = $this->page->getTranslations('draft_meta_description');
        $this->meta_description = ! empty($draftMetaDescription) ? $draftMetaDescription : $this->page->getTranslations('meta_description');

        $this->no_index = $this->page->draft_no_index !== null ? $this->page->draft_no_index : $this->page->no_index;
    }

    /**
     * Get available locales from settings.
     *
     * @return array<string, string>
     */
    protected function getAvailableLocales(): array
    {
        $localesSetting = app('settings')->get('general.available_locales', []);

        return collect($localesSetting)->pluck('name', 'code')->all();
    }

    /**
     * Handle locale switching.
     */
    public function updatedSwitchLocale(string $locale): void
    {
        if (array_key_exists($locale, $this->availableLocales)) {
            $this->redirect(route('admin.pages.editor', ['page' => $this->page, 'locale' => $locale]));
        }
    }

    /**
     * Get the blocks for the current page.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlocksProperty()
    {
        return $this->page->contentBlocks()->ordered()->get();
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
     * Start editing a specific block.
     */
    public function editBlock(int $blockId): void
    {
        $block = ContentBlock::find($blockId);

        if (! $block) {
            $this->showWarningToast(
                __('messages.block_editor.block_not_found_text'),
                __('messages.block_editor.block_not_found_title')
            );

            return;
        }

        $this->editingBlockId = $blockId;
        $this->editingBlockVisible = $block->isVisible();
        $this->editingBlockImageUpload = null;

        // Load block data - prefer draft data if available, otherwise use published data
        $blockClass = $this->blockManager->find($block->type);
        $defaultData = $blockClass instanceof \App\Blocks\Block ? $blockClass->getDefaultData() : [];

        // Use draft data if available, otherwise fall back to published data
        $blockData = $block->hasDraftChanges()
            ? $block->getDraftTranslatedData($this->activeLocale)
            : $block->getTranslatedData($this->activeLocale);

        $blockSettings = $block->hasDraftChanges()
            ? $block->getDraftSettingsArray()
            : $block->getSettingsArray();

        $this->editingBlockState = array_merge($defaultData, $blockData, $blockSettings);
    }

    /**
     * Cancel editing the current block.
     */
    public function cancelBlockEdit(): void
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->editingBlockVisible = true;
        $this->editingBlockImageUpload = null;
    }

    /**
     * Handle updates to the editing block state.
     */
    public function updatedEditingBlockState(): void
    {
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
        }
    }

    /**
     * Handle updates to the editing block visibility.
     */
    public function updatedEditingBlockVisible(bool $value): void
    {
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
        }
    }

    /**
     * Create a new block of the specified type.
     */
    public function createBlock(string $type, CreateContentBlockAction $createContentBlockAction): void
    {
        try {
            $block = $createContentBlockAction->execute($this->page, $type, $this->availableLocales);

            $this->showSuccessToast(
                __('messages.page_manager.block_created_text'),
                __('messages.page_manager.block_created_title')
            );

            // Start editing the new block
            $this->editBlock($block->id);

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_creation_failed_text'),
                __('messages.page_manager.block_creation_failed_title')
            );
        }
    }

    /**
     * Update the order of blocks.
     */
    public function updateBlockOrder(array $sort, UpdateBlockOrderAction $updateBlockOrderAction): void
    {
        try {
            $updateBlockOrderAction->execute($this->page, $sort);

            $this->showSuccessToast(
                __('messages.page_manager.block_order_updated_text'),
                __('messages.page_manager.block_order_updated_title')
            );

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_order_update_failed_text'),
                __('messages.page_manager.block_order_update_failed_title')
            );
        }
    }

    /**
     * Confirm deletion of a block.
     */
    public function confirmDeleteBlock(int $blockId): void
    {
        $this->confirm(__('messages.page_manager.confirm_delete_block_text'), [
            'onConfirmed' => 'deleteBlock',
            'onConfirmedParams' => [$blockId],
        ]);
    }

    /**
     * Delete a block after confirmation.
     */
    #[On('deleteBlockConfirmed')]
    public function deleteBlock(int $blockId): void
    {
        try {
            $deleteContentBlockAction = app(DeleteContentBlockAction::class);
            $deleteContentBlockAction->execute($blockId);

            $this->showSuccessToast(
                __('messages.page_manager.block_deleted_text'),
                __('messages.page_manager.block_deleted_title')
            );

            // Cancel editing if the deleted block was being edited
            if ($this->editingBlockId === $blockId) {
                $this->cancelBlockEdit();
            }

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_deletion_failed_text'),
                __('messages.page_manager.block_deletion_failed_title')
            );
        }
    }

    /**
     * Generate a slug from the page title.
     */
    public function generateSlug(): void
    {
        $title = $this->title[$this->activeLocale] ?? '';
        $this->slug = Str::slug($title);
    }

    /**
     * Save the page details.
     */
    public function savePage(): void
    {
        try {
            $saveDraftPageDetailsAction = app(SaveDraftPageDetailsAction::class);
            $saveDraftPageDetailsAction->execute($this->page, [
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'no_index' => $this->no_index,
            ]);

            $this->showSuccessToast(
                __('messages.page_manager.page_saved_text'),
                __('messages.page_manager.page_saved_title')
            );

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.page_save_failed_text'),
                __('messages.page_manager.page_save_failed_title')
            );
        }
    }

    /**
     * Save the current block's draft state.
     */
    protected function saveCurrentBlockDraft(): void
    {
        if (! $this->editingBlockId) {
            return;
        }

        try {
            $saveDraftContentBlockAction = app(SaveDraftContentBlockAction::class);
            $saveDraftContentBlockAction->execute($this->editingBlockId, $this->editingBlockState, $this->editingBlockVisible);
        } catch (\Exception $e) {
            // Silently fail for auto-save operations
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.page-manager')
            ->layout('layouts.admin');
    }
}
