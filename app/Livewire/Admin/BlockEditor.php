<?php

namespace App\Livewire\Admin;

use App\Actions\Content\UpdateContentBlockAction;
use App\Enums\ContentBlockStatus;
use App\Facades\Settings;
use App\Models\ContentBlock;
use App\Services\BlockManager;
use App\Traits\WithToastNotifications;
use App\Traits\WithConfirmationModal;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Reactive;

class BlockEditor extends Component
{
    use WithFileUploads, WithToastNotifications, WithConfirmationModal;

    public ?ContentBlock $editingBlock = null;

    public array $state = [];
    public $imageUpload;
    public string $formTitle = '';
    public ContentBlockStatus $blockStatus = ContentBlockStatus::DRAFT;
    public bool $isPublished = false;
    public string $activeLocale;

    protected BlockManager $blockManager;
    protected $lastAutosaveTime = null;

    protected $listeners = [
        'editBlock' => 'startEditing',
        'autosave' => 'autosave',
        'localeSwitched' => 'localeSwitched',
        'repeater-updated' => 'onRepeaterUpdate',
        'changeBlockStatus' => 'onStatusConfirmed',
    ];

    public function boot(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    public function mount(string $activeLocale)
    {
        $this->activeLocale = $activeLocale;
    }

    public function updated(string $name, mixed $value): void
    {
        if ($name === 'activeLocale') {
            $this->activeLocale = $value;
            if ($this->editingBlock) {
                $this->cancelEdit();
            }
        }
    }

    public function localeSwitched(string $locale): void
    {
        $this->activeLocale = $locale;
        if ($this->editingBlock) {
            $this->cancelEdit();
        }
    }

    public function startEditing(int $blockId): void
    {
        $this->editingBlock = ContentBlock::find($blockId);

        if (! $this->editingBlock) {
            $this->showWarningToast(
                __('messages.block_editor.block_not_found_text'),
                __('messages.block_editor.block_not_found_title')
            );
            $this->cancelEdit();

            return;
        }

        $blockClass = $this->blockManager->find($this->editingBlock->type);
        $this->formTitle = 'Editing: ' . ($blockClass ? $blockClass->getName() : 'Block');

        $defaultData = $blockClass ? $blockClass->getDefaultData() : [];
        $this->state = array_merge($defaultData, $this->editingBlock->data ?? [], $this->editingBlock->settings ?? []);

        $this->blockStatus = $this->editingBlock->status;
        $this->isPublished = $this->editingBlock->status === ContentBlockStatus::PUBLISHED;
        $this->imageUpload = null;
        $this->lastAutosaveTime = now();

        $this->dispatch('block-is-editing', state: $this->state);
        $this->updatedState();

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
            __('messages.block_editor.block_updated_text'),
            __('messages.block_editor.block_updated_title')
        );

        $this->finishEditing();
        $this->dispatch('block-was-updated');
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
        $this->showSuccessToast(__('messages.block_editor.autosaved'), duration: 2000);
    }

    public function updatedIsPublished(bool $value): void
    {
        $this->isPublished = !$value;

        $title = $value ? __('messages.block_editor.publish_confirmation_title') : __('messages.block_editor.unpublish_confirmation_title');
        $message = $value ? __('messages.block_editor.publish_confirmation_text') : __('messages.block_editor.unpublish_confirmation_text');

        $this->confirmAction(
            title: $title,
            message: $message,
            action: 'changeBlockStatus',
            data: ['newStatus' => $value]
        );
    }

    public function onStatusConfirmed(array $data)
    {
        if (! $this->editingBlock) {
            return;
        }

        $this->isPublished = $data['newStatus'];
        $this->blockStatus = $this->isPublished ? ContentBlockStatus::PUBLISHED : ContentBlockStatus::DRAFT;

        app(UpdateContentBlockAction::class)->execute(
            $this->editingBlock,
            $this->state,
            $this->activeLocale,
            $this->blockStatus,
            null,
            $this->blockManager
        );

        $this->showSuccessToast(__('messages.block_editor.block_updated_text'));
        $this->dispatch('block-status-updated');
    }

    protected function finishEditing()
    {
        $this->editingBlock = null;
        $this->state = [];
        $this->imageUpload = null;
        $this->blockStatus = ContentBlockStatus::DRAFT;
        $this->isPublished = false;
        $this->lastAutosaveTime = null;
        $this->dispatch('stopAutosaveTimer');
    }

    public function updatedState()
    {
        $this->dispatch('block-state-updated', id: $this->editingBlock->id, state: $this->state);
    }

    public function onRepeaterUpdate(array $data): void
    {
        $this->state[str_replace('state.', '', $data['model'])] = $data['items'];
        $this->updatedState();
    }

    public function render()
    {
        return view('livewire.admin.block-editor');
    }
}
