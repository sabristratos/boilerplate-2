<div>
    @if($editingBlock)
        <div
            class="flex-1"
            x-data="{ state: $wire.entangle('state') }"
            x-init="$watch('state', value => {
                window.dispatchEvent(new CustomEvent('block-state-updated', {
                    detail: {
                        id: '{{ $editingBlock->id }}',
                        state: value
                    }
                }));
            })"
        >
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <flux:heading size="sm">{{ $formTitle }}</flux:heading>
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" variant="subtle">{{ __('messages.block_editor.status') }}</flux:text>
                        <flux:select wire:model.live="blockStatus" size="sm" class="w-32">
                            @foreach(\App\Enums\ContentBlockStatus::cases() as $status)
                                <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveBlock" class="space-y-4">
                @csrf
                @php($blockClass = $this->blockManager->find($editingBlock->type))
                @if($blockClass)
                    <div class="overflow-y-auto p-2 max-h-[calc(100vh-250px)]">
                        @include($blockClass->getAdminView(), ['alpine' => true])
                    </div>
                @endif

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button type="button" wire:click="cancelEdit" variant="subtle" size="sm">
                        {{ __('buttons.cancel') }}
                    </flux:button>
                    <flux:button type="submit" @click="$wire.set('state', state)" variant="primary" size="sm">
                        {{ __('buttons.save_changes') }}
                    </flux:button>
                </div>
            </form>

        </div>
    @endif
</div>
