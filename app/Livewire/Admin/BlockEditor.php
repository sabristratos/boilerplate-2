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
        if (!is_array($data)) {
            $data = [];
        }
        
        $blockId = $data['blockId'] ?? null;
        
        if ($blockId) {
            // Save current block state before switching
            if ($this->editingBlockId && $this->editingBlockId !== $blockId) {
                $this->saveCurrentBlockDraft();
            }
            
            $this->editingBlockId = $blockId;
            $this->loadBlockData();
            
            // Dispatch event to notify PageCanvas of the editing state change
            $this->dispatch('block-editing-started', [
                'blockId' => $this->editingBlockId,
                'blockState' => $this->editingBlockState
            ]);
        }
    }

    /**
     * Handle cancel block edit events.
     */
    #[On('cancel-block-edit')]
    public function handleCancelBlockEdit(): void
    {
        // Save current state before canceling
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
        }

        // Clear editing state
        $this->editingBlockId = null;
        $this->editingBlockState = [];
        $this->editingBlockVisible = true;
        $this->editingBlockImageUpload = null;

        // Dispatch event to notify PageCanvas that editing was cancelled
        $this->dispatch('block-editing-cancelled');
    }

    /**
     * Called at the end of every component request.
     */
    public function dehydrate(): void
    {
        // Note: Auto-save is now handled via debounced events to reduce database load
    }

    /**
     * Load block data for editing.
     */
    protected function loadBlockData(): void
    {
        if (!$this->editingBlockId) {
            return;
        }

        $block = ContentBlock::find($this->editingBlockId);

        if (!$block) {
            return;
        }

        // Clear previous state
        $this->editingBlockState = [];
        $this->editingBlockImageUpload = null;

        // Set new editing state
        $this->editingBlockVisible = $block->isVisible();

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
            // Dispatch event to update Alpine data in the preview immediately
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState
            ]);
            
            // Update PageCanvas editing state
            $this->dispatch('block-editing-started', [
                'blockId' => $this->editingBlockId,
                'blockState' => $this->editingBlockState
            ]);
            
            // Debounce the save operation to avoid excessive database writes
            $this->dispatch('debounced-save-block');
        }
    }

    /**
     * Handle updates to the editing block visibility.
     */
    public function updatedEditingBlockVisible(bool $value): void
    {
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
            
            // Dispatch event to update Alpine data in the preview
            $this->dispatch('block-state-updated', [
                'id' => $this->editingBlockId,
                'state' => $this->editingBlockState
            ]);
        }
    }

    /**
     * Handle debounced save events.
     */
    #[On('debounced-save-block')]
    public function handleDebouncedSave(): void
    {
        if ($this->editingBlockId) {
            $this->saveCurrentBlockDraft();
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
            $contentBlock = ContentBlock::find($this->editingBlockId);
            
            if (!$contentBlock) {
                return;
            }

            $saveDraftContentBlockAction = app(SaveDraftContentBlockAction::class);
            $saveDraftContentBlockAction->execute(
                $contentBlock,
                $this->editingBlockState,
                $this->activeLocale,
                $this->editingBlockVisible,
                $this->editingBlockImageUpload,
                $this->blockManager
            );
        } catch (\Exception $e) {
            // Silently fail for auto-save operations
        }
    }

    /**
     * Get the current block being edited.
     */
    #[Computed]
    public function getCurrentBlockProperty()
    {
        if (!$this->editingBlockId) {
            return null;
        }

        return ContentBlock::find($this->editingBlockId);
    }

    /**
     * Get the block class for the current block.
     */
    #[Computed]
    public function getCurrentBlockClassProperty()
    {
        $block = $this->currentBlock;
        
        if (!$block) {
            return null;
        }

        return $this->blockManager->find($block->type);
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

    /**
     * Get the current editing state for the parent component.
     */
    #[Computed]
    public function getEditingStateProperty(): array
    {
        return [
            'editingBlockId' => $this->editingBlockId,
            'editingBlockState' => $this->editingBlockState,
        ];
    }
} 