<?php

namespace App\Livewire\Admin;

use App\Actions\Content\UpdateContentBlockAction;
use App\Enums\ContentBlockStatus;
use App\Facades\Settings;
use App\Models\ContentBlock;
use App\Models\Page;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Support\Str;
use App\Services\BlockManager;

class PageManager extends Component
{
    use WithFileUploads;

    public Page $page;
    public array $title = [];
    public array $slug = [];
    public string $activeLocale;

    public ?ContentBlock $editingBlock = null;

    public array $state = [];

    public $imageUpload;

    public string $formTitle = '';

    public ?ContentBlockStatus $blockStatus = null;

    protected BlockManager $blockManager;

    protected $lastAutosaveTime = null;

    protected $listeners = ['autosave' => 'autosave'];

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
        Log::info('Editing block', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_id' => $blockId,
        ]);

        $this->editingBlock = ContentBlock::find($blockId);

        if (!$this->editingBlock) {
            Flux::toast(
                heading: 'Block Not Found',
                text: 'The content block you are trying to edit no longer exists.',
                variant: 'warning'
            );
            $this->cancelEdit();
            return;
        }

        $blockClass = $this->blockManager->find($this->editingBlock->type);
        $this->formTitle = 'Editing: ' . ($blockClass ? $blockClass->getName() : 'Block');

        // With the locale set in render(), the model's accessor will now correctly
        // retrieve the translated data, including defaults.
        $this->state = $this->editingBlock->data ?? [];

        $this->dispatch('block-is-editing', state: $this->state);

        $this->blockStatus = $this->editingBlock->status ?? ContentBlockStatus::DRAFT;
        $this->imageUpload = null;
        $this->lastAutosaveTime = now();

        // Start autosave timer if enabled
        if (Settings::get('content.autosave_enabled', true)) {
            $this->dispatch('startAutosaveTimer',
                interval: Settings::get('content.autosave_interval', 30) * 1000
            );
        }
    }

    public function saveBlock(UpdateContentBlockAction $updateAction)
    {
        if (! $this->editingBlock) {
            return;
        }

        Log::info('Saving block', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_id' => $this->editingBlock->id,
        ]);

        $this->authorize('update', $this->editingBlock);

        $blockClass = $this->blockManager->find($this->editingBlock->type);
        if ($blockClass) {
            $rules = collect($blockClass->validationRules())
                ->mapWithKeys(fn ($rule, $key) => ['state.' . $key => $rule])
                ->all();

            $this->validate($rules);
        }

        $updateAction->execute(
            $this->editingBlock,
            $this->state,
            $this->activeLocale,
            $this->blockStatus,
            $this->imageUpload,
            $this->blockManager
        );

        Log::info('Block saved successfully', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_id' => $this->editingBlock->id,
        ]);

        Flux::toast(
            heading: 'Block Updated',
            text: 'The content block was updated successfully.',
            variant: 'success'
        );

        $this->editingBlock = null;
        $this->blockStatus = null;
        $this->lastAutosaveTime = null;

        // Stop autosave timer
        $this->dispatch('stopAutosaveTimer');
    }

    public function cancelEdit()
    {
        if ($this->editingBlock) {
            Log::info('Cancelled block edit', [
                'user_id' => auth()->id(),
                'page_id' => $this->page->id,
                'block_id' => $this->editingBlock->id,
            ]);
        }

        $this->editingBlock = null;
        $this->state = [];
        $this->imageUpload = null;
        $this->blockStatus = null;
        $this->lastAutosaveTime = null;

        // Stop autosave timer
        $this->dispatch('stopAutosaveTimer');
    }

    public function createBlock(string $type): void
    {
        Log::info('Creating block', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_type' => $type,
        ]);

        $blockClass = $this->blockManager->find($type);

        if (!$blockClass) {
            Flux::toast(
                heading: 'Invalid Block Type',
                text: 'The block type you are trying to create is not available.',
                variant: 'danger'
            );
            return;
        }

        $block = $this->page->contentBlocks()->create([
            'type' => $blockClass->getType(),
            'data' => $blockClass->getDefaultData(),
            'status' => ContentBlockStatus::DRAFT,
        ]);

        Log::info('Block created successfully', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_id' => $block->id,
        ]);

        Flux::toast(
            heading: 'Block Created',
            text: 'A new ' . $blockClass->getName() . ' block has been added to the page.',
            variant: 'success'
        );
    }

    public function updateBlockOrder(array $sort): void
    {
        Log::info('Updating block order', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'order' => $sort,
        ]);
        try {
            ContentBlock::setNewOrder($sort);
            Flux::toast(
                text: 'Block order updated successfully.',
                variant: 'subtle',
                duration: 2000
            );
        } catch (\Exception $e) {
            Log::error('Error updating block order', [
                'user_id' => auth()->id(),
                'page_id' => $this->page->id,
                'error' => $e->getMessage(),
            ]);
            Flux::toast(
                heading: 'Error Updating Order',
                text: 'There was a problem updating the block order. Some blocks may no longer exist.',
                variant: 'danger'
            );
        }
    }

    public function deleteBlock(int $blockId): void
    {
        Log::info('Deleting block', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
            'block_id' => $blockId,
        ]);

        $block = ContentBlock::find($blockId);
        if ($block) {
            // Check if the block being deleted is currently being edited
            if ($this->editingBlock && $this->editingBlock->id === $blockId) {
                $this->cancelEdit();
            }

            $block->delete();
            Flux::toast('Block deleted successfully.');
        } else {
            Flux::toast(
                heading: 'Block Not Found',
                text: 'The content block you are trying to delete no longer exists.',
                variant: 'warning'
            );
        }
    }

    public function getPreviewBlockProperty()
    {
        if (!$this->editingBlock) {
            return null;
        }

        // Create a temporary block with the current state for preview
        $previewBlock = new ContentBlock([
            'type' => $this->editingBlock->type,
            'data' => $this->state,
        ]);

        // If the original block has media, we'll use it for the preview
        if ($this->editingBlock->hasMedia('images')) {
            $previewBlock->setRelation('media', $this->editingBlock->media);
        }

        return $previewBlock;
    }

    /**
     * Autosave the current block if enough time has passed
     */
    public function autosave()
    {
        if (!$this->editingBlock || !Settings::get('content.autosave_enabled', true)) {
            return;
        }

        // Check if enough time has passed since the last autosave
        $interval = Settings::get('content.autosave_interval', 30);
        $now = now();

        if ($this->lastAutosaveTime && $now->diffInSeconds($this->lastAutosaveTime) < $interval) {
            return;
        }

        // Verify the block still exists in the database
        $existingBlock = ContentBlock::find($this->editingBlock->id);
        if (!$existingBlock) {
            Flux::toast(
                heading: 'Block Not Found',
                text: 'The content block you are editing no longer exists. Your changes cannot be saved.',
                variant: 'danger'
            );
            $this->cancelEdit();
            return;
        }

        // Update the block with the current state
        $this->editingBlock->data = $this->state;
        $this->editingBlock->status = ContentBlockStatus::DRAFT;
        $this->editingBlock->save();

        $this->lastAutosaveTime = $now;

        // Show a subtle notification
        Flux::toast(
            text: 'Draft saved automatically.',
            variant: 'subtle',
            duration: 2000
        );
    }

    /**
     * Set the block status
     */
    public function setBlockStatus(string $status)
    {
        try {
            $this->blockStatus = ContentBlockStatus::from($status);
        } catch (\ValueError $e) {
            Flux::toast(
                heading: 'Invalid Status',
                text: 'The provided block status is not valid.',
                variant: 'danger'
            );
        }
    }

    public function generateSlug(string $locale): void
    {
        if (isset($this->title[$locale])) {
            $this->slug[$locale] = Str::slug($this->title[$locale]);
        }
    }

    public function savePageDetails()
    {
        Log::info('Saving page details', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
        ]);

        $this->validate([
            'title.' . $this->activeLocale => 'required|string|max:255',
            'slug.' . $this->activeLocale => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug->' . $this->activeLocale)->ignore($this->page->id),
            ],
        ]);

        $this->page->setTranslations('title', $this->title);
        $this->page->setTranslations('slug', $this->slug);
        $this->page->save();

        Log::info('Page details saved successfully', [
            'user_id' => auth()->id(),
            'page_id' => $this->page->id,
        ]);

        Flux::toast(
            heading: 'Page Updated',
            text: 'The page details were updated successfully.',
            variant: 'success'
        );
    }

    public function render()
    {
        app()->setLocale($this->activeLocale);
        return view('livewire.admin.page-manager');
    }
}
