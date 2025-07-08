<div 
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
            <div class="p-6 pb-4">
                <div class="flex justify-between items-center">
                    <flux:heading size="sm">
                        {{ $this->currentBlockClass ? 'Editing: ' . $this->currentBlockClass->getName() : 'Editing Block' }}
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
            </div>

            <!-- Block Form -->
            @if($this->currentBlockClass)
                <div class="overflow-y-auto flex-1" style="height: calc(100vh - 200px);">
                    <div class="px-6">
                        @include($this->currentBlockClass->getAdminView(), [
                            'alpine' => true, 
                            'editingBlockState' => $editingBlockState,
                            'editingBlock' => $this->currentBlock
                        ])
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="p-6 flex flex-col items-center justify-center py-12 text-center">
            <flux:icon name="pencil-square" class="w-12 h-12 text-zinc-400 mb-4" />
            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('messages.page_manager.no_block_selected') }}</flux:heading>
            <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.select_block_to_edit') }}</flux:text>
        </div>
    @endif
</div> 