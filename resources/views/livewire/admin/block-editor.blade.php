<div class="p-6" 
    x-data="{
        debouncedSave: null,
        init() {
            this.$wire.on('debounced-save-block', () => {
                // Clear any existing timeout
                if (this.debouncedSave) {
                    clearTimeout(this.debouncedSave);
                }
                
                // Set a new timeout to save after 1 second of inactivity
                this.debouncedSave = setTimeout(() => {
                    this.$wire.call('handleDebouncedSave');
                }, 1000);
            });
        }
    }"
>
    @if($editingBlockId)
        <div class="space-y-4">
            <!-- Block Header -->
            <div class="flex justify-between items-center">
                <flux:heading size="sm">
                    {{ $currentBlockClass ? 'Editing: ' . $currentBlockClass->getName() : 'Editing Block' }}
                </flux:heading>
                <div class="flex items-center gap-4">
                    <flux:field :label="__('messages.page_manager.visibility')" variant="inline">
                        <flux:switch
                            wire:model.live="editingBlockVisible"
                        />
                    </flux:field>
                    <flux:badge
                        :color="$editingBlockVisible ? 'lime' : 'zinc'"
                        variant="solid"
                    >
                       {{ $editingBlockVisible ? __('messages.page_manager.visible') : __('messages.page_manager.hidden') }}
                    </flux:badge>
                </div>
            </div>

            <!-- Block Form -->
            @if($currentBlockClass)
                <div class="overflow-y-auto max-h-[calc(100vh-300px)]">
                    @include($currentBlockClass->getAdminView(), [
                        'alpine' => true, 
                        'editingBlockState' => $editingBlockState,
                        'editingBlock' => $currentBlock
                    ])
                </div>
            @endif

            <!-- Block Actions -->
            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button type="button" wire:click="$dispatch('cancel-block-edit')" variant="subtle" size="sm">
                    {{ __('buttons.cancel') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <flux:icon name="pencil-square" class="w-12 h-12 text-zinc-400 mb-4" />
            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('messages.page_manager.no_block_selected') }}</flux:heading>
            <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.select_block_to_edit') }}</flux:text>
        </div>
    @endif
</div> 