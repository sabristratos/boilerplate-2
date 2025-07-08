<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\ContentBlock;
use App\Services\Contracts\BlockEditorServiceInterface;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Block Editor component for editing individual content blocks.
 */
class BlockEditor extends Component
{
    use WithFileUploads, WithToastNotifications;

    /**
     * Currently active locale for editing.
     */
    #[Reactive]
    public string $activeLocale;

    /**
     * ID of the block currently being edited.
     */
    public ?int $editingBlockId = null;

    /**
     * The page being edited (for context).
     */
    public \App\Models\Page $page;

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

    /**
     * Block manager service instance.
     */
    protected BlockManager $blockManager;

    /**
     * Block editor service instance.
     */
    protected BlockEditorServiceInterface $blockEditorService;

    /**
     * Mount the component with the page to edit.
     */
    public function mount(\App\Models\Page $page, string $activeLocale): void
    {
        $this->page = $page;
        $this->activeLocale = $activeLocale;
    }

    /**
     * Boot the component and inject dependencies.
     */
    public function boot(BlockManager $blockManager, BlockEditorServiceInterface $blockEditorService): void
    {
        $this->blockManager = $blockManager;
        $this->blockEditorService = $blockEditorService;
    }

    /**
     * Handle edit block events from PageCanvas.
     */
    #[On('edit-block')]
    public function handleEditBlock($data = null): void
    {
        $blockId = $data['blockId'] ?? null;

        if (! $blockId) {
            return;
        }

        $block = $this->blockEditorService->getBlockById($blockId);

        if (!$block instanceof \App\Models\ContentBlock) {
            return;
        }

        // Set editing state
        $this->editingBlockId = $blockId;
        $this->editingBlockVisible = $this->blockEditorService->getBlockVisibility($block);

        // Load block data
        $this->editingBlockState = $this->blockEditorService->loadBlockData($block, $this->activeLocale);

        // Clear any previous image upload
        $this->editingBlockImageUpload = null;
    }

    /**
     * Handle cancel block edit events from PageCanvas.
     */
    #[On('cancel-block-edit')]
    public function handleCancelBlockEdit(): void
    {
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->editingBlockImageUpload = null;
    }

    /**
     * Handle block deletion events from PageCanvas.
     */
    #[On('block-deleted')]
    public function handleBlockDeleted($data): void
    {
        $deletedBlockId = $data['blockId'] ?? null;

        // Clear editing state if the deleted block was being edited
        if ($this->editingBlockId === $deletedBlockId) {
            $this->editingBlockId = null;
            $this->editingBlockState = [];
            $this->editingBlockImageUpload = null;
        }
    }

    /**
     * Handle updates to the editing block state.
     */
    public function updatedEditingBlockState(): void
    {
        if ($this->editingBlockId !== null && $this->editingBlockId !== 0) {
            // Dispatch event to update PageCanvas immediately
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState,
            ]);
        }
    }

    /**
     * Handle visibility changes.
     */
    public function updatedEditingBlockVisible(): void
    {
        if ($this->editingBlockId !== null && $this->editingBlockId !== 0) {
            $this->dispatch('block-visibility-updated', [
                'id' => $this->editingBlockId,
                'visible' => $this->editingBlockVisible,
            ]);
        }
    }

    /**
     * Handle repeater updates from child components.
     */
    #[On('repeater-updated')]
    public function handleRepeaterUpdated($data): void
    {
        if (isset($data['model']) && isset($data['items'])) {
            $modelPath = $data['model'];
            $items = $data['items'];

            // Update the editingBlockState with the new items
            $this->editingBlockState = $this->blockEditorService->updateRepeaterStateInArray(
                $this->editingBlockState,
                $modelPath,
                $items
            );

            // Immediately notify the parent of the state change
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState,
            ]);
        }
    }

    /**
     * Handle media updates from media uploader.
     */
    #[On('media-updated')]
    public function handleMediaUpdated($modelId = null, $collection = null, $isTemporary = false): void
    {
        if ($this->editingBlockId && $this->editingBlockId === $modelId) {
            $block = $this->blockEditorService->getBlockById($this->editingBlockId);
            if ($block instanceof \App\Models\ContentBlock) {
                // Update the state based on the new media status
                $this->editingBlockState = $this->blockEditorService->updateMediaState($this->editingBlockState, $block, $collection);

                // Immediately notify the parent of the state change
                $this->dispatch('block-state-updated', [
                    'id' => $this->editingBlockId,
                    'state' => $this->editingBlockState,
                ]);
            }
        }
    }

    /**
     * Handle debounced save of block data.
     */
    public function handleDebouncedSave(): void
    {
        if ($this->editingBlockId && $this->editingBlockState !== []) {
            // Here you would implement the actual save logic
            // For now, we'll just dispatch an event to notify that save is needed
            $this->dispatch('block-save-needed', [
                'blockId' => $this->editingBlockId,
                'state' => $this->editingBlockState,
            ]);
        }
    }

    /**
     * Cancel editing the current block.
     */
    public function cancelEditing(): void
    {
        $this->handleCancelBlockEdit();
        $this->dispatch('block-edit-canceled');
    }

    /**
     * Get the current block being edited.
     */
    #[Computed]
    public function getCurrentBlockProperty(): ?ContentBlock
    {
        if ($this->editingBlockId === null || $this->editingBlockId === 0) {
            return null;
        }

        return $this->blockEditorService->getBlockById($this->editingBlockId);
    }

    /**
     * Get the current block class being edited.
     */
    #[Computed]
    public function getCurrentBlockClassProperty(): ?\App\Blocks\Block
    {
        $block = $this->getCurrentBlockProperty();

        if (!$block instanceof \App\Models\ContentBlock) {
            return null;
        }

        return $this->blockEditorService->getBlockClass($block);
    }

    /**
     * Get the current editing state for a specific block.
     */
    public function getEditingStateForBlock(int $blockId): ?array
    {
        if ($this->editingBlockId === $blockId) {
            return $this->editingBlockState;
        }

        return null;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.block-editor');
    }
}
