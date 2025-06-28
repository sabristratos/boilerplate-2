<?php

namespace App\Livewire\Admin;

use App\Enums\ContentBlockStatus;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class PageManager extends Component
{
    use WithToastNotifications, WithFileUploads;

    public Page $page;
    public array $title = [];
    public array $slug = [];
    public string $activeLocale;

    protected BlockManager $blockManager;

    protected $listeners = [
        'blockEditCancelled' => '$refresh',
    ];

    

    public function boot(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $this->page->getTranslations('title');
        $this->slug = $this->page->getTranslations('slug');
        $this->activeLocale = request()->query('locale', config('app.locale'));
        app()->setLocale($this->activeLocale);
    }

    public function getBlocksProperty()
    {
        return $this->page->contentBlocks()->ordered()->get();
    }

    public function editBlock(int $blockId): void
    {
        $this->dispatch('editBlock', $blockId);
    }

    public function createBlock(string $type): void
    {
        $blockClass = $this->blockManager->find($type);

        if (! $blockClass) {
            $this->showErrorToast(
                __('messages.page_manager.invalid_block_type_text'),
                __('messages.page_manager.invalid_block_type_title')
            );

            return;
        }

        $block = $this->page->contentBlocks()->create([
            'type' => $blockClass->getType(),
            'data' => $blockClass->getDefaultData(),
            'status' => ContentBlockStatus::DRAFT,
        ]);

        $this->showSuccessToast(
            __('messages.page_manager.block_created_text', ['blockName' => $blockClass->getName()]),
            __('messages.page_manager.block_created_title')
        );

        $this->editBlock($block->id);
    }

    public function updateBlockOrder(array $sort): void
    {
        try {
            ContentBlock::setNewOrder($sort);
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

    public function deleteBlock(int $blockId): void
    {
        $this->page->contentBlocks()->find($blockId)?->delete();
        $this->showSuccessToast(__('messages.page_manager.block_deleted_text'));
    }

    public function generateSlug(string $locale): void
    {
        $this->slug[$locale] = Str::slug($this->title[$locale]);
    }

    public function savePageDetails()
    {
        $this->validate([
            'title.'.$this->activeLocale => 'required|string|max:255',
            'slug.'.$this->activeLocale => 'required|string|max:255',
        ]);

        foreach ($this->title as $locale => $value) {
            $this->page->setTranslation('title', $locale, $value);
        }

        foreach ($this->slug as $locale => $value) {
            $this->page->setTranslation('slug', $locale, $value);
        }

        $this->page->save();

        $this->showSuccessToast(__('messages.page_manager.page_details_saved_text'));
    }

    public function render()
    {
        return view('livewire.admin.page-manager')->title(
            'Editing: ' . $this->page->getTranslation('title', 'en', false)
        );
    }
}
