<div class="grid grid-cols-1 md:grid-cols-4 gap-4"
    x-data="{
        autosaveTimer: null,
        startAutosaveTimer(interval) {
            this.stopAutosaveTimer();
            this.autosaveTimer = setInterval(() => {
                $wire.autosave();
            }, interval);
        },
        stopAutosaveTimer() {
            if (this.autosaveTimer) {
                clearInterval(this.autosaveTimer);
                this.autosaveTimer = null;
            }
        }
    }"
    x-on:startautosavetimer.window="startAutosaveTimer($event.detail.interval)"
    x-on:stopautosavetimer.window="stopAutosaveTimer()"
>
    {{-- Main content blocks --}}
    <div class="md:col-span-3 space-y-4">
        <div class="space-y-4"
            x-data="{
                reorder(event) {
                    const container = event.target;
                    const items = container.querySelectorAll('[x-sort\\:item]');
                    const ids = Array.from(items).map(item => item.getAttribute('x-sort:item'));
                    this.$wire.call('updateBlockOrder', ids);
                }
            }"
            x-sort
            x-on:sort.stop="reorder($event)">
            @forelse($this->blocks as $block)
                <div x-sort:item="{{ $block->id }}" wire:key="block-{{ $block->id }}" class="relative group">
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                        <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <flux:button wire:click="editBlock({{ $block->id }})" size="xs" variant="filled" icon="pencil-square" />
                            <flux:button wire:click="deleteBlock({{ $block->id }})" size="xs" variant="danger" icon="trash" />
                        </div>
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 flex items-center">
                            <flux:badge size="sm">{{ Str::title(str_replace('-', ' ', $block->type)) }}</flux:badge>
                            @if($block->status)
                                <flux:badge size="sm" color="{{ $block->status->color() }}" class="ml-2">
                                    {{ $block->status->label() }}
                                </flux:badge>
                            @endif
                            <div class="ml-auto cursor-grab" x-sort:handle>
                                <flux:icon name="bars-3" class="h-4 w-4 text-zinc-400" />
                            </div>
                        </div>
                        <div class="relative" wire:click="editBlock({{ $block->id }})">
                            @php($blockClass = $this->blockManager->find($block->type))
                            @if($blockClass)
                                <div class="cursor-pointer hover:opacity-90 transition-opacity">
                                    @if($editingBlock && $editingBlock->id === $block->id && $this->previewBlock)
                                        @include($blockClass->getFrontendView(), ['block' => $this->previewBlock])
                                    @else
                                        @include($blockClass->getFrontendView(), ['block' => $block])
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center p-12 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <flux:icon name="layout-grid" class="h-10 w-10 text-zinc-400" />
                    <flux:text class="mt-4">This page has no content yet.</flux:text>
                    <flux:text variant="subtle">Add a block from the library to get started.</flux:text>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="md:col-span-1 flex flex-col h-full">
        {{-- Block Library --}}
        <div class="space-y-2 mb-4">
            <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Block Library</h3>
            <div class="space-y-1 max-h-48 overflow-y-auto p-1">
                @foreach($this->blockManager->getAvailableBlocks() as $block)
                    <button wire:click="createBlock('{{ $block->getType() }}')" type="button" class="w-full text-left p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 text-sm">
                        <div class="flex items-center">
                            <flux:icon name="{{ $block->getIcon() }}" class="h-4 w-4 mr-2" />
                            {{ $block->getName() }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Block Editor --}}
        @if($editingBlock)
            <div class="flex-1 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <flux:heading size="sm">{{ $formTitle }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:text size="sm" variant="subtle">Status:</flux:text>
                            <flux:select wire:model.live="blockStatus" size="sm" class="w-32">
                                @foreach(\App\Enums\ContentBlockStatus::cases() as $status)
                                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="saveBlock" class="space-y-4">
                    @php($blockClass = $this->blockManager->find($editingBlock->type))
                    @if($blockClass)
                        <div class="overflow-y-auto max-h-[calc(100vh-300px)]">
                            @include($blockClass->getAdminView())
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button type="button" wire:click="cancelEdit" variant="subtle" size="sm">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary" size="sm">
                            Save changes
                        </flux:button>
                    </div>
                </form>

            </div>
        @endif
    </div>

</div>
