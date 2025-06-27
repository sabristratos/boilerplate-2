<?php

namespace App\Livewire\Admin;

use App\Actions\Content\UpdateContentBlockAction;
use App\Enums\ContentBlockStatus;
use App\Facades\Settings;
use App\Models\ContentBlock;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use Livewire\Component;
use Livewire\WithFileUploads;

class BlockEditor extends Component
{
    use WithFileUploads, WithToastNotifications;

    public ?ContentBlock $editingBlock = null;
    public array $state = [];
    public $imageUpload;
    public string $formTitle = '';
    public ?ContentBlockStatus $blockStatus = null;
    public string $activeLocale;

    protected BlockManager $blockManager;
    protected $lastAutosaveTime = null;

    protected $listeners = [
        'editBlock' => 'startEditing',
        'autosave' => 'autosave',
    ];

    public function boot(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    public function mount(string $activeLocale)
    {
        $this->activeLocale = $activeLocale;
    }

    public function startEditing(int $blockId): void
    {
        $this->editingBlock = ContentBlock::find($blockId);

        if (! $this->editingBlock) {
            $this->showWarningToast(
                __('The content block you are trying to edit no longer exists.'),
                __('Block Not Found')
            );
            $this->cancelEdit();

            return;
        }

        $blockClass = $this->blockManager->find($this->editingBlock->type);
        $this->formTitle = 'Editing: ' . ($blockClass ? $blockClass->getName() : 'Block');
        $this->state = array_merge($this->editingBlock->data ?? [], $this->editingBlock->settings ?? []);
        $this->blockStatus = $this->editingBlock->status ?? ContentBlockStatus::DRAFT;
        $this->imageUpload = null;
        $this->lastAutosaveTime = now();

        $this->dispatch('block-is-editing', state: $this->state);

        if (Settings::get('content.autosave_enabled', true)) {
            $this->dispatch(
                'startAutosaveTimer',
                interval: Settings::get('content.autosave_interval', 30) * 1000
            );
        }
    }

    public function saveBlock(UpdateContentBlockAction $updateAction)
    {
        if (! $this->editingBlock) {
            return;
        }

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

        $this->showSuccessToast(
            __('The content block was updated successfully.'),
            __('Block Updated')
        );

        $this->finishEditing();
        $this->dispatch('blockUpdated');
    }

    public function cancelEdit()
    {
        $this->finishEditing();
        $this->dispatch('blockEditCancelled');
    }

    public function autosave()
    {
        if (! $this->editingBlock) {
            return;
        }

        if ($this->lastAutosaveTime && now()->diffInSeconds($this->lastAutosaveTime) < 5) {
            return;
        }

        $this->saveBlock(app(UpdateContentBlockAction::class));

        $this->lastAutosaveTime = now();
        $this->showSuccessToast(__('Autosaved'), duration: 2000);
    }

    public function setBlockStatus(string $status)
    {
        $this->blockStatus = ContentBlockStatus::tryFrom($status);
    }

    protected function finishEditing()
    {
        $this->editingBlock = null;
        $this->state = [];
        $this->imageUpload = null;
        $this->blockStatus = null;
        $this->lastAutosaveTime = null;
        $this->dispatch('stopAutosaveTimer');
    }

    public function render()
    {
        return view('livewire.admin.block-editor');
    }
}
