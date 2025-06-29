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
use App\Traits\WithConfirmationModal;
use Livewire\Attributes\On;
use App\Enums\PublishStatus;

class PageManager extends Component
{
    use WithToastNotifications, WithFileUploads, WithConfirmationModal;

    public Page $page;
    public array $title = [];
    public ?string $slug = '';
    public PublishStatus $status;
    public array $meta_title = [];
    public array $meta_description = [];
    public bool $no_index = false;
    public string $activeLocale;
    public array $availableLocales = [];
    public array $editingBlockState = [];
    public ?int $editingBlockId = null;
    public string $activeSidebarTab = 'settings';
    public ?string $switchLocale = null;
    public bool $isPublished;

    protected BlockManager $blockManager;

    protected $listeners = [
        'blockEditCancelled' => 'onBlockEditCancelled',
        'block-was-updated' => 'onBlockEditFinished',
        'block-state-updated' => 'onBlockStateUpdate',
        'changePageStatus' => 'onStatusConfirmed',
        'block-status-updated' => '$refresh',
    ];

    #[On('deleteBlockConfirmed')]
    public function deleteBlock(int $blockId): void
    {
        if (! $blockId) {
            return;
        }

        app(DeleteContentBlockAction::class)->execute($blockId);
        $this->showSuccessToast(__('messages.page_manager.block_deleted_text'));
        $this->dispatch('$refresh');
    }

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
        $this->status = $this->page->status;
        $this->isPublished = $this->page->status === PublishStatus::PUBLISHED;
        $this->meta_title = $this->page->getTranslations('meta_title');
        $this->meta_description = $this->page->getTranslations('meta_description');
        $this->no_index = $this->page->no_index;
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

    public function confirmDeleteBlock(int $blockId): void
    {
        $this->confirmDelete($blockId, 'deleteBlockConfirmed');
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
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'no_index' => $this->no_index,
        ]);

        $this->showSuccessToast(__('messages.page_manager.page_details_saved_text'));
    }

    public function getIsPublishedProperty(): bool
    {
        return $this->status === PublishStatus::PUBLISHED;
    }

    public function updatedIsPublished(bool $value): void
    {
        $this->isPublished = !$value;

        $title = $value ? __('messages.page_manager.publish_confirmation_title') : __('messages.page_manager.unpublish_confirmation_title');
        $message = $value ? __('messages.page_manager.publish_confirmation_text') : __('messages.page_manager.unpublish_confirmation_text');

        $this->confirmAction(
            title: $title,
            message: $message,
            action: 'changePageStatus',
            data: ['newStatus' => $value]
        );
    }

    public function onStatusConfirmed(array $data)
    {
        $this->isPublished = $data['newStatus'];
        $this->status = $this->isPublished ? PublishStatus::PUBLISHED : PublishStatus::DRAFT;
        $this->savePageDetails(app(\App\Actions\Content\UpdatePageDetailsAction::class));
    }

    public function render()
    {
        return view('livewire.admin.page-manager')
            ->layout('components.layouts.editors', [
                'title' => 'Editing: ' . $this->page->getTranslation('title', $this->activeLocale, false)
            ]);
    }
}
