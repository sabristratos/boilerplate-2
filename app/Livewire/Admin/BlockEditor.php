<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Content\SaveDraftContentBlockAction;
use App\Models\ContentBlock;
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
    use WithToastNotifications, WithFileUploads;

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
    public function boot(BlockManager $blockManager): void
    {
        $this->blockManager = $blockManager;
    }

    /**
     * Handle edit block events from PageCanvas.
     */
    #[On('edit-block')]
    public function handleEditBlock($data = null): void
    {
        $blockId = $data['blockId'] ?? null;

        if (!$blockId) {
            return;
        }

        $block = ContentBlock::find($blockId);

        if (!$block) {
            return;
        }

        // Set editing state
        $this->editingBlockId = $blockId;
        $this->editingBlockVisible = $block->isVisible();

        // Load block data
        $this->loadBlockData($block);

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
     * Load block data for editing.
     */
    protected function loadBlockData(ContentBlock $block): void
    {
        // Clear previous state
        $this->editingBlockState = [];

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
     * Handle updates to the editing block state.
     */
    public function updatedEditingBlockState(): void
    {
        if ($this->editingBlockId) {
            // Dispatch event to update PageCanvas immediately
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState
            ]);
            
            // Debounce the save operation to avoid excessive database writes
            $this->dispatch('debounced-save-block');
        }
    }

    /**
     * Handle visibility changes.
     */
    public function updatedEditingBlockVisible(): void
    {
        if ($this->editingBlockId) {
            // Save the visibility change immediately
            $this->saveBlockDraft();
        }
    }

    /**
     * Handle debounced save requests.
     */
    #[On('debounced-save-block')]
    public function handleDebouncedSave(): void
    {
        if ($this->editingBlockId) {
            $this->saveBlockDraft();
        }
    }

    /**
     * Handle repeater updates from child components.
     */
    #[On('repeater-updated')]
    public function handleRepeaterUpdated($data): void
    {
        if (isset($data['model']) && isset($data['items'])) {
            // Update the editingBlockState with the new items
            $modelPath = $data['model'];
            $items = $data['items'];
            
            // Convert dot notation to array path
            $pathParts = explode('.', $modelPath);
            $current = &$this->editingBlockState;
            
            foreach ($pathParts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
            
            $current = $items;
            
            // Dispatch event to update PageCanvas immediately
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState
            ]);
            
            // Debounce the save operation
            $this->dispatch('debounced-save-block');
        }
    }

    /**
     * Handle media updates from media uploader.
     */
    #[On('media-updated')]
    public function handleMediaUpdated($modelId = null, $collection = null, $isTemporary = false): void
    {
        if ($this->editingBlockId && $collection) {
            // Get the current block
            $block = ContentBlock::find($this->editingBlockId);
            
            if (!$block) {
                return;
            }

            // Get the media URL for the collection (will be null if removed)
            $media = $block->getFirstMedia($collection);
            $mediaUrl = $media ? $media->getUrl() : null;

            // Update the editingBlockState with the media URL (or null if removed)
            // For hero section, this would be background_image
            if ($collection === 'background_image') {
                $this->editingBlockState['background_image'] = $mediaUrl;
            }
            // Add more collections as needed
            // elseif ($collection === 'other_collection') {
            //     $this->editingBlockState['other_field'] = $mediaUrl;
            // }

            // Dispatch event to update PageCanvas immediately
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState
            ]);
            
            // Debounce the save operation
            $this->dispatch('debounced-save-block');
        }
    }

    /**
     * Save the current block as a draft.
     */
    public function saveBlockDraft(): void
    {
        if (!$this->editingBlockId) {
            return;
        }

        try {
            $block = ContentBlock::find($this->editingBlockId);

            if (!$block) {
                return;
            }

            $saveDraftContentBlockAction = app(SaveDraftContentBlockAction::class);
            $saveDraftContentBlockAction->execute(
                $block,
                $this->editingBlockState,
                $this->activeLocale,
                $this->editingBlockVisible,
                $this->editingBlockImageUpload,
                $this->blockManager
            );

            // No success toast needed since user can see changes live

        } catch (\Exception $e) {
            $this->showErrorToast(
                __('messages.block_editor.block_save_failed_text'),
                __('messages.block_editor.block_save_failed_title')
            );
        }
    }

    /**
     * Cancel editing the current block.
     */
    public function cancelEditing(): void
    {
        $this->dispatch('cancel-block-edit');
    }

    /**
     * Get the current block being edited.
     */
    #[Computed]
    public function getCurrentBlockProperty(): ?ContentBlock
    {
        if (!$this->editingBlockId) {
            return null;
        }

        return ContentBlock::find($this->editingBlockId);
    }

    /**
     * Get the current block class being edited.
     */
    #[Computed]
    public function getCurrentBlockClassProperty(): ?\App\Blocks\Block
    {
        $block = $this->currentBlock;
        
        if (!$block) {
            return null;
        }

        return $this->blockManager->find($block->type);
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
        return view('livewire.admin.block-editor', [
            'blockManager' => $this->blockManager,
            'currentBlock' => $this->currentBlock,
            'currentBlockClass' => $this->currentBlockClass
        ]);
    }
} 