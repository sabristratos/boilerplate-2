@props(['blocks', 'editingBlockId', 'editingBlockState', 'blockManager'])

<div class="flex-1 overflow-y-auto">
    <div 
        class="space-y-0 p-0"
        x-data="{
            reorder(event) {
                try {
                    const container = event.target;
                    const items = container.querySelectorAll('[x-sort\\:item]');
                    const ids = Array.from(items).map(item => item.getAttribute('x-sort:item')).filter(id => id);
                    if (ids.length > 0) {
                        this.$wire.call('updateBlockOrder', ids);
                    }
                } catch (error) {
                    console.warn('Failed to reorder blocks:', error);
                }
            },
            
            handleBlockStateUpdated(event) {
                try {
                    const blockElement = document.querySelector(`[data-block-id='${event.detail.id}']`);
                    if (blockElement && blockElement._x_dataStack && blockElement._x_dataStack[0]) {
                        const alpineComponent = blockElement._x_dataStack[0];
                        if (alpineComponent.data) {
                            Object.assign(alpineComponent.data, event.detail.state);
                        }
                    }
                } catch (error) {
                    console.warn('Failed to update block state:', error);
                }
            },
            
            handleBlockEditStarted(event) {
                try {
                    // Clear all previous editing states
                    document.querySelectorAll('[data-block-id]').forEach(element => {
                        if (element._x_dataStack && element._x_dataStack[0]) {
                            const alpineComponent = element._x_dataStack[0];
                            if (alpineComponent.data && alpineComponent.data.isEditing) {
                                alpineComponent.data.isEditing = false;
                            }
                        }
                    });
                    
                    // Set the new editing state
                    const blockElement = document.querySelector(`[data-block-id='${event.detail.id}']`);
                    if (blockElement && blockElement._x_dataStack && blockElement._x_dataStack[0]) {
                        const alpineComponent = blockElement._x_dataStack[0];
                        if (alpineComponent.data) {
                            alpineComponent.data.isEditing = true;
                            Object.assign(alpineComponent.data, event.detail.state);
                        }
                    }
                } catch (error) {
                    console.warn('Failed to start block edit:', error);
                }
            },
            
            handleBlockEditCancelled() {
                try {
                    // Clear all editing states
                    document.querySelectorAll('[data-block-id]').forEach(element => {
                        if (element._x_dataStack && element._x_dataStack[0]) {
                            const alpineComponent = element._x_dataStack[0];
                            if (alpineComponent.data && alpineComponent.data.isEditing) {
                                alpineComponent.data.isEditing = false;
                            }
                        }
                    });
                } catch (error) {
                    console.warn('Failed to cancel block edit:', error);
                }
            }
        }"
        x-sort
        x-on:sort.stop="reorder($event)"
        @block-state-updated.window="handleBlockStateUpdated($event)"
        @block-edit-started.window="handleBlockEditStarted($event)"
        @block-edit-cancelled.window="handleBlockEditCancelled()"
    >
        @forelse($blocks as $block)
            @if($block->isVisible())
                <div x-sort:item="{{ $block->id }}" wire:key="block-{{ $block->id }}" data-block-id="{{ $block->id }}" class="relative group block-preview-section" tabindex="0" style="will-change: border-color, transform;">
                    <!-- Block Content (as real page) with border highlight on hover/focus -->
                    <div class="transition-colors duration-150 border-2 border-transparent group-hover:border-blue-400 group-focus-within:border-blue-400 group-active:border-blue-500">
                        <!-- Block Actions (Edit/Delete/Drag) -->
                        <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity z-20" wire:click.stop>
                            <div class="cursor-grab hover:cursor-grabbing mr-2 text-zinc-400 hover:text-blue-500 transition-colors" x-sort:handle>
                                <flux:icon name="bars-3" class="h-4 w-4" />
                            </div>
                            <flux:button 
                                wire:click="editBlock({{ $block->id }})" 
                                size="xs" 
                                variant="subtle" 
                                icon="pencil-square" 
                                :tooltip="__('Edit')"
                            />
                            <flux:button 
                                wire:click="confirmDeleteBlock({{ $block->id }})" 
                                size="xs" 
                                variant="danger" 
                                icon="trash" 
                                :tooltip="__('Delete')"
                            />
                        </div>

                        <div class="relative cursor-pointer" wire:click="editBlock({{ $block->id }})" style="pointer-events: auto;">
                            @php
                                $blockClass = $blockManager->find($block->type);
                                $alpine = false;
                                
                                if ($editingBlockId === $block->id && $editingBlockState && is_array($editingBlockState)) {
                                    // When editing, use the editing state as the primary data
                                    $data = $editingBlockState;
                                    $alpine = true;
                                } else {
                                    // When not editing, use draft data if available, otherwise use published data
                                    if ($block->hasDraftChanges()) {
                                        $data = array_merge($block->getDraftTranslatedData(app()->getLocale()), $block->getDraftSettingsArray());
                                    } else {
                                        $data = array_merge($block->getTranslatedData(app()->getLocale()), $block->getSettingsArray());
                                    }
                                }
                            @endphp

                            @if($blockClass)
                                @include($blockClass->getFrontendView(), ['block' => $block, 'data' => $data, 'alpine' => $alpine])
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Hidden Block Placeholder -->
                <div wire:key="block-hidden-{{ $block->id }}" class="relative group block-preview-section" tabindex="0">
                    <div class="transition-colors duration-150 border-2 border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:icon name="eye-slash" class="h-5 w-5 text-zinc-400" />
                                <div>
                                    <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                        {{ $blockManager->find($block->type)?->getName() ?? 'Hidden Block' }}
                                    </flux:text>
                                    <flux:text variant="subtle" class="text-xs">{{ __('messages.page_manager.hidden_block') }}</flux:text>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <flux:button 
                                    wire:click="editBlock({{ $block->id }})" 
                                    size="xs" 
                                    variant="subtle" 
                                    icon="pencil-square" 
                                    :tooltip="__('Edit')"
                                />
                                <flux:button 
                                    wire:click="confirmDeleteBlock({{ $block->id }})" 
                                    size="xs" 
                                    variant="danger" 
                                    icon="trash" 
                                    :tooltip="__('Delete')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center p-12 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                <flux:icon name="layout-grid" class="h-12 w-12 text-zinc-400 mb-4" />
                <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400 mb-2">{{ __('messages.page_manager.no_content_yet') }}</flux:heading>
                <flux:text variant="subtle" class="text-center">{{ __('messages.page_manager.add_blocks_from_library') }}</flux:text>
            </div>
        @endforelse
    </div>
</div>

<style>
.block-preview-section > div:focus-within .group-focus-within\:border-blue-400 {
    border-color: #60a5fa !important;
}
</style> 