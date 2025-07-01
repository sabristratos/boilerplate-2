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
                    <div class="flex items-center gap-4">
                        <flux:field :label="__('messages.page_manager.status')" variant="inline">
                            <flux:switch
                                wire:model.live="isPublished"
                            />
                        </flux:field>
                        <flux:badge
                            :color="$isPublished ? 'lime' : 'zinc'"
                            variant="solid"
                        >
                           {{ $isPublished ? __('messages.page_manager.published') : __('messages.page_manager.draft') }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveBlock" class="space-y-4">
                @csrf
                @php($blockClass = $this->blockManager->find($editingBlock->type))
                @if($blockClass)
                    <div class="overflow-y-auto p-2 max-h-[calc(100vh-300px)]">
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
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <flux:icon name="pencil-square" class="w-12 h-12 text-zinc-400 mb-4" />
            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('messages.page_manager.no_block_selected') }}</flux:heading>
            <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.select_block_to_edit') }}</flux:text>
        </div>
    @endif
</div>
