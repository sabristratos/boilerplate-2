<?php

namespace App\Livewire\Admin;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Actions\Content\CreateContentBlockAction;
use App\Actions\Content\UpdatePageDetailsAction;
use App\Actions\Content\UpdateBlockOrderAction;
use App\Actions\Content\DeleteContentBlockAction;

class PageManager extends Component
{
    use WithToastNotifications, WithFileUploads;

    public Page $page;
    public array $title = [];
    public ?string $slug = '';
    public string $activeLocale;
    public array $availableLocales = [];
    public array $editingBlockState = [];
    public ?int $editingBlockId = null;
    public string $activeSidebarTab = 'settings';
    public ?string $switchLocale = null;

    protected BlockManager $blockManager;

    protected $listeners = [
        'blockEditCancelled' => 'onBlockEditCancelled',
        'block-was-updated' => 'onBlockEditFinished',
        'block-state-updated' => 'onBlockStateUpdate'
    ];

    public function onBlockEditFinished()
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->activeSidebarTab = 'settings';
        $this->dispatch('$refresh');
    }

    public function onBlockEditCancelled()
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->activeSidebarTab = 'settings';
    }

    public function onBlockStateUpdate(int $id, array $state)
    {
        $this->editingBlockId = $id;
        $this->editingBlockState = $state;
    }

    public function boot(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->initializeLocale();
        $this->loadPageTranslations();
    }

    protected function initializeLocale(): void
    {
        $this->availableLocales = $this->getAvailableLocales();
        $this->activeLocale = request()->query('locale', config('app.fallback_locale'));

        if (!array_key_exists($this->activeLocale, $this->availableLocales)) {
            $this->activeLocale = config('app.fallback_locale');
        }
        $this->switchLocale = $this->activeLocale;
        app()->setLocale($this->activeLocale);
    }

    protected function loadPageTranslations(): void
    {
        $this->title = $this->page->getTranslations('title');
        $this->slug = $this->page->slug;
    }

    protected function getAvailableLocales(): array
    {
        $localesSetting = app('settings')->get('general.available_locales', []);

        return collect($localesSetting)->pluck('name', 'code')->all();
    }

    public function updatedSwitchLocale($locale): void
    {
        // Add debugging toast to verify method is called
        $this->showSuccessToast("Switching locale to: " . $locale);

        if (array_key_exists($locale, $this->availableLocales)) {
            $this->redirect(route('admin.pages.editor', ['page' => $this->page, 'locale' => $locale]));
        }
    }

    public function getBlocksProperty()
    {
        return $this->page->contentBlocks()->ordered()->get();
    }

    public function editBlock(int $blockId): void
    {
        $this->activeSidebarTab = 'edit';
        $this->dispatch('editBlock', $blockId);
    }

    public function createBlock(string $type, CreateContentBlockAction $createContentBlockAction): void
    {
        try {
            $block = $createContentBlockAction->execute($this->page, $type, $this->availableLocales);
            $this->showSuccessToast(
                __('messages.page_manager.block_created_text', ['blockName' => $block->blockClass->getName()]),
                __('messages.page_manager.block_created_title')
            );
            $this->editBlock($block->id);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_order_update_error_text'),
                __('messages.page_manager.block_order_update_error_title')
            );
        }
    }

    public function deleteBlock(int $blockId, DeleteContentBlockAction $deleteContentBlockAction): void
    {
        $deleteContentBlockAction->execute($blockId);
        $this->showSuccessToast(__('messages.page_manager.block_deleted_text'));
    }

    public function generateSlug(): void
    {
        $fallbackLocale = config('app.fallback_locale');
        $title = $this->title[$fallbackLocale] ?? '';
        $this->slug = Str::slug($title);
    }

    public function savePageDetails(UpdatePageDetailsAction $updatePageDetailsAction): void
    {
        $updatePageDetailsAction->execute($this->page, [
            'title' => $this->title,
            'slug' => $this->slug,
        ]);

        $this->showSuccessToast(__('messages.page_manager.page_details_saved_text'));
    }

    public function render()
    {
        return view('livewire.admin.page-manager')
            ->layout('components.layouts.app', [
                'title' => 'Editing: ' . $this->page->getTranslation('title', $this->activeLocale, false)
            ]);
    }
}
