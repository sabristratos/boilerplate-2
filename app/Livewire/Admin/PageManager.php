<?php

namespace App\Livewire\Admin;

use App\Actions\Content\CreateContentBlockAction;
use App\Actions\Content\DeleteContentBlockAction;
use App\Actions\Content\SaveDraftContentBlockAction;
use App\Actions\Content\SaveDraftPageDetailsAction;
use App\Actions\Content\UpdateBlockOrderAction;
use App\Actions\Content\UpdatePageDetailsAction;
use App\Enums\ContentBlockStatus;
use App\Enums\PublishStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PageManager extends Component
{
    use WithConfirmationModal, WithFileUploads, WithToastNotifications;

    public Page $page;

    // Page-level properties (draft versions)
    public array $title = [];
    public ?string $slug = '';
    public array $meta_title = [];
    public array $meta_description = [];
    public bool $no_index = false;

    // Locale management
    public string $activeLocale;
    public array $availableLocales = [];
    public ?string $switchLocale = null;

    // Block editing state
    public ?int $editingBlockId = null;
    public array $editingBlockState = [];
    public bool $editingBlockVisible = true;
    public $editingBlockImageUpload;

    // UI state
    public string $tab = 'settings';

    // Block library filtering
    public string $blockSearch = '';
    public string $selectedCategory = '';
    public string $selectedComplexity = '';

    protected BlockManager $blockManager;

    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    public function mount(Page $page): void
    {
        $this->authorize('update', $page);
        
        $this->page = $page;
        $this->initializeLocale();
        $this->loadPageTranslations();
    }

    protected function initializeLocale(): void
    {
        $this->availableLocales = $this->getAvailableLocales();
        $requestedLocale = request()->query('locale', config('app.fallback_locale'));
        
        // Validate locale format (2-3 character language code)
        if (!preg_match('/^[a-z]{2,3}$/', $requestedLocale)) {
            $requestedLocale = config('app.fallback_locale');
        }
        
        $this->activeLocale = array_key_exists($requestedLocale, $this->availableLocales) 
            ? $requestedLocale 
            : config('app.fallback_locale');
            
        $this->switchLocale = $this->activeLocale;
        app()->setLocale($this->activeLocale);
    }

    protected function loadPageTranslations(): void
    {
        // Load draft data if available, otherwise fall back to published data
        $draftTitle = $this->page->getTranslations('draft_title');
        $this->title = !empty($draftTitle) ? $draftTitle : $this->page->getTranslations('title');
        
        $this->slug = $this->page->draft_slug ?? $this->page->slug;
        
        $draftMetaTitle = $this->page->getTranslations('draft_meta_title');
        $this->meta_title = !empty($draftMetaTitle) ? $draftMetaTitle : $this->page->getTranslations('meta_title');
        
        $draftMetaDescription = $this->page->getTranslations('draft_meta_description');
        $this->meta_description = !empty($draftMetaDescription) ? $draftMetaDescription : $this->page->getTranslations('meta_description');
        
        $this->no_index = $this->page->draft_no_index !== null ? $this->page->draft_no_index : $this->page->no_index;
    }

    protected function getAvailableLocales(): array
    {
        $localesSetting = app('settings')->get('general.available_locales', []);

        return collect($localesSetting)->pluck('name', 'code')->all();
    }

    public function updatedSwitchLocale(string $locale): void
    {
        if (array_key_exists($locale, $this->availableLocales)) {
            $this->redirect(route('admin.pages.editor', ['page' => $this->page, 'locale' => $locale]));
        }
    }

    public function getBlocksProperty()
    {
        return $this->page->contentBlocks()->ordered()->get();
    }

    public function getFilteredBlocksProperty()
    {
        $blocks = $this->blockManager->getAvailableBlocks();

        // Filter by search
        if (!empty($this->blockSearch)) {
            $blocks = $blocks->filter(function ($block) {
                return str_contains(strtolower($block->getName()), strtolower($this->blockSearch)) ||
                       str_contains(strtolower($block->getDescription()), strtolower($this->blockSearch)) ||
                       collect($block->getTags())->contains(function ($tag) {
                           return str_contains(strtolower($tag), strtolower($this->blockSearch));
                       });
            });
        }

        // Filter by category
        if (!empty($this->selectedCategory)) {
            $blocks = $blocks->filter(function ($block) {
                return $block->getCategory() === $this->selectedCategory;
            });
        }

        // Filter by complexity
        if (!empty($this->selectedComplexity)) {
            $blocks = $blocks->filter(function ($block) {
                return $block->getComplexity() === $this->selectedComplexity;
            });
        }

        return $blocks;
    }

    public function getAvailableCategoriesProperty()
    {
        return $this->blockManager->getAvailableBlocks()
            ->pluck('category')
            ->unique()
            ->values()
            ->all();
    }

    public function getAvailableComplexitiesProperty()
    {
        return $this->blockManager->getAvailableBlocks()
            ->pluck('complexity')
            ->unique()
            ->values()
            ->all();
    }

    public function editBlock(int $blockId): void
    {
        $block = ContentBlock::find($blockId);
        
        if (!$block) {
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
        
        // Merge in the correct order: defaults -> data -> settings (settings override everything)
        $this->editingBlockState = array_merge($defaultData, $blockData, $blockSettings);
    }

    public function cancelBlockEdit(): void
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->editingBlockVisible = true;
        $this->editingBlockImageUpload = null;
    }

    public function updatedEditingBlockState(): void
    {
        // Auto-save draft changes as the user types
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
        }
    }

    public function updatedEditingBlockVisible(bool $value): void
    {
        // Auto-save visibility changes
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
            $this->dispatch('$refresh');
        }
    }

    public function createBlock(string $type, CreateContentBlockAction $createContentBlockAction): void
    {
        try {
            $block = $createContentBlockAction->execute($this->page, $type, $this->availableLocales);
            $this->showSuccessToast(
                __('messages.page_manager.block_created_text', ['blockName' => $block->blockClass->getName()]),
                __('messages.page_manager.block_created_title')
            );
            $this->dispatch('$refresh');
        } catch (\Exception) {
            $this->showErrorToast(
                __('messages.page_manager.invalid_block_type_text'),
                __('messages.page_manager.invalid_block_type_title')
            );
        }
    }

    public function updateBlockOrder(array $sort, UpdateBlockOrderAction $updateBlockOrderAction): void
    {
        try {
            $updateBlockOrderAction->execute($sort);
            $this->showSuccessToast(
                __('messages.page_manager.block_order_updated_text'),
                null,
                2000
            );
        } catch (\Exception) {
            $this->showErrorToast(
                __('messages.page_manager.block_order_update_error_text'),
                __('messages.page_manager.block_order_update_error_title')
            );
        }
    }

    public function confirmDeleteBlock(int $blockId): void
    {
        $this->confirmDelete($blockId, 'deleteBlockConfirmed');
    }

    #[On('deleteBlockConfirmed')]
    public function deleteBlock(int $blockId): void
    {
        if ($blockId === 0) {
            return;
        }

        app(DeleteContentBlockAction::class)->execute($blockId);
        $this->showSuccessToast(__('messages.page_manager.block_deleted_text'));
        $this->dispatch('$refresh');
    }

    public function generateSlug(): void
    {
        $fallbackLocale = config('app.fallback_locale');
        $title = $this->title[$fallbackLocale] ?? '';
        $this->slug = Str::slug($title);
    }

    public function savePage(): void
    {
        // Save page draft details first
        app(SaveDraftPageDetailsAction::class)->execute($this->page, [
            'title' => $this->title,
            'slug' => $this->slug,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'no_index' => $this->no_index,
        ]);

        // Save the currently editing block if any
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
        }

        // Publish all drafts to make them live
        $this->page->publishDraft();

        $this->showSuccessToast(__('messages.page_manager.page_published_text'));
        $this->dispatch('$refresh');
    }

    protected function saveCurrentBlockDraft(): void
    {
        if (!$this->editingBlockId) {
            return;
        }

        $block = ContentBlock::find($this->editingBlockId);
        if (!$block) {
            return;
        }

        // Validate block data
        $blockClass = $this->blockManager->find($block->type);
        if ($blockClass instanceof \App\Blocks\Block) {
            $rules = collect($blockClass->validationRules())
                ->mapWithKeys(fn ($rule, $key) => ['editingBlockState.'.$key => $rule])
                ->all();

            $this->validate($rules);
        }

        // Save to draft
        app(SaveDraftContentBlockAction::class)->execute(
            $block,
            $this->editingBlockState,
            $this->activeLocale,
            $this->editingBlockVisible,
            $this->editingBlockImageUpload,
            $this->blockManager
        );
    }

    public function render()
    {
        return view('livewire.admin.page-manager')
            ->layout('components.layouts.editors', [
                'title' => 'Editing: '.$this->page->getTranslation('title', $this->activeLocale, false),
            ]);
    }
}
