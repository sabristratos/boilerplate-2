@props(['blocks', 'editingBlockId', 'editingBlockState', 'blockManager'])

<div class="flex-1 overflow-y-auto">
    <div 
        class="space-y-0 p-0"
        x-data="{
            reorder(event) {
                const container = event.target;
                const items = container.querySelectorAll('[x-sort\\:item]');
                const ids = Array.from(items).map(item => item.getAttribute('x-sort:item'));
                this.$wire.call('updateBlockOrder', ids);
            }
        }"
        x-sort
        x-on:sort.stop="reorder($event)"
    >
        @forelse($blocks as $block)
            <div x-sort:item="{{ $block->id }}" wire:key="block-{{ $block->id }}" class="relative group block-preview-section" tabindex="0" style="will-change: border-color, transform;">
                <!-- Block Content (as real page) with border highlight on hover/focus -->
                <div class="transition-colors duration-150 border-2 border-transparent group-hover:border-blue-400 group-focus-within:border-blue-400 group-active:border-blue-500 rounded-lg">
                    <!-- Block Actions (Edit/Delete/Drag) -->
                    <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity z-20">
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

                    <div class="relative cursor-pointer" wire:click.self="editBlock({{ $block->id }})">
                        @php
                            $blockClass = $blockManager->find($block->type);
                            $data = is_array($block->data) ? $block->data : [];
                            $alpine = false;
                            if ($editingBlockId === $block->id && $editingBlockState && is_array($editingBlockState)) {
                                $data = array_merge($data, $editingBlockState);
                                $alpine = true;
                            }
                        @endphp

                        @if($blockClass)
                            @include($blockClass->getFrontendView(), ['block' => $block, 'data' => $data, 'alpine' => $alpine])
                        @endif
                    </div>
                </div>
            </div>
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