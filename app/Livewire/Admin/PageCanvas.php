<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Content\DeleteContentBlockAction;
use App\Actions\Content\SaveDraftContentBlockAction;
use App\Actions\Content\UpdateBlockOrderAction;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Services\BlockManager;
use App\Traits\WithConfirmationModal;
use App\Traits\WithToastNotifications;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Page Canvas component for displaying and managing content blocks.
 */
class PageCanvas extends Component
{
    use WithConfirmationModal, WithFileUploads, WithToastNotifications;

    /**
     * The page being edited.
     */
    public Page $page;

    /**
     * Currently active locale for editing.
     */
    #[Reactive]
    public string $activeLocale;

    /**
     * ID of the block currently being edited (for view state).
     */
    public ?int $editingBlockId = null;

    /**
     * State data for the block being edited (for view state).
     *
     * @var array<string, mixed>
     */
    public array $editingBlockState = [];

    /**
     * Block manager service instance.
     */
    protected BlockManager $blockManager;



    /**
     * Mount the component with the page to edit.
     */
    public function mount(Page $page, string $activeLocale): void
    {
        $this->page = $page;
        $this->activeLocale = $activeLocale;
    }

    /**
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
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

        // Dispatch event to notify BlockEditor component
        $this->dispatch('edit-block', ['blockId' => $blockId]);
    }

    /**
     * Cancel editing the current block.
     */
    public function cancelBlockEdit(): void
    {
        // Dispatch event to notify BlockEditor component
        $this->dispatch('cancel-block-edit');
    }

    /**
     * Handle block editing started events from BlockEditor.
     */
    #[On('block-editing-started')]
    public function handleBlockEditingStarted($data): void
    {
        $this->editingBlockId = $data['blockId'] ?? null;
        $this->editingBlockState = $data['blockState'] ?? [];
    }

    /**
     * Handle block editing cancelled events from BlockEditor.
     */
    #[On('block-editing-cancelled')]
    public function handleBlockEditingCancelled(): void
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
    }



    /**
     * Update the order of blocks.
     */
    public function updateBlockOrder(array $sort): void
    {
        try {
            $updateBlockOrderAction = app(UpdateBlockOrderAction::class);
            $updateBlockOrderAction->execute($this->page, $sort);

            $this->dispatch('block-order-updated', ['sort' => $sort]);

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
        $this->confirmAction(
            __('messages.page_manager.confirm_delete_block_title'),
            __('messages.page_manager.confirm_delete_block_text'),
            'deleteBlock',
            ['blockId' => $blockId]
        );
    }



    /**
     * Delete a block after confirmation.
     */
    #[On('deleteBlock')]
    public function deleteBlock($data): void
    {
        // Ensure data is an array
        if (!is_array($data)) {
            $data = [];
        }
        
        $blockId = $data['blockId'] ?? null;
        
        if (!$blockId) {
            $this->showErrorToast('Invalid block ID provided');
            return;
        }

        try {
            $deleteContentBlockAction = app(DeleteContentBlockAction::class);
            $deleteContentBlockAction->execute($blockId);

            $this->dispatch('block-deleted', ['blockId' => $blockId]);

            // Cancel editing if the deleted block was being edited
            $this->dispatch('cancel-block-edit');

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.page_manager.block_deletion_failed_text'),
                __('messages.page_manager.block_deletion_failed_title')
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
        return view('livewire.admin.page-canvas', [
            'blockManager' => $this->blockManager
        ]);
    }
} 