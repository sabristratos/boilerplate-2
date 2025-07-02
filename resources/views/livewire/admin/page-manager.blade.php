<div
    x-data="{
        liveState: null,
        editingBlockId: null,
    }"
    @block-state-updated.window="
        editingBlockId = $event.detail.id;
        liveState = $event.detail.state;
    "
    @block-edit-cancelled.window="
        editingBlockId = null;
        liveState = null;
    "
    class="flex flex-col h-screen bg-zinc-100 dark:bg-zinc-900 font-sans"
>
    <!-- Unified Header -->
    <x-page-builder.header 
        :page="$page" 
        :availableLocales="$availableLocales" 
        :activeLocale="$activeLocale" 
        :switchLocale="$switchLocale" 
    />

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left Panel: Toolbox & Settings -->
        <div class="w-96 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col overflow-visible">
            <x-page-builder.toolbox 
                :tab="$tab" 
                :activeLocale="$activeLocale" 
                :blockManager="$this->blockManager"
            />
        </div>

        <!-- Center Panel: Canvas -->
        <div class="flex-1 flex flex-col">
            <x-page-builder.page-canvas 
                :blocks="$this->blocks" 
                :editingBlockId="$editingBlockId" 
                :editingBlockState="$editingBlockState" 
                :blockManager="$this->blockManager"
            />
        </div>

        <!-- Right Panel: Properties -->
        <div class="w-[400px] bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto shrink-0">
            <div class="p-6">
                @if($editingBlockId)
                    <div class="space-y-4">
                        <!-- Block Header -->
                        <div class="flex justify-between items-center">
                            @php
                                $block = \App\Models\ContentBlock::find($editingBlockId);
                                $blockClass = $block ? $this->blockManager->find($block->type) : null;
                            @endphp
                            <flux:heading size="sm">
                                {{ $blockClass ? 'Editing: ' . $blockClass->getName() : 'Editing Block' }}
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
                        @if($blockClass)
                            <div class="overflow-y-auto max-h-[calc(100vh-300px)]">
                                @include($blockClass->getAdminView(), [
                                    'alpine' => true, 
                                    'state' => $editingBlockState,
                                    'editingBlock' => $block
                                ])
                            </div>
                        @endif

                        <!-- Block Actions -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button type="button" wire:click="cancelBlockEdit" variant="subtle" size="sm">
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
        </div>
    </div>
</div>
