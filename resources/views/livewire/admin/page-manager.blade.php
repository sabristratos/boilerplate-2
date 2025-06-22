<div>
    <div class="mb-4">
        <a href="{{ route('admin.pages.index') }}" wire:navigate class="text-sm text-zinc-500 hover:text-zinc-700">
            &larr; Back to pages
        </a>
    </div>

    <form wire:submit.prevent="savePageDetails" class="mb-6 pb-6 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-4">
            <flux:heading>
                Edit Page: {{ $page->getTranslation('title', $activeLocale) ?: 'New Translation' }} ({{ strtoupper($activeLocale) }})
            </flux:heading>
            <flux:button type="submit" variant="primary">Save Page</flux:button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:input
                    wire:model.defer="title.{{ $activeLocale }}"
                    wire:change="generateSlug('{{ $activeLocale }}')"
                    label="Title"
                    id="title_{{ $activeLocale }}"
                />
                @error('title.'.$activeLocale) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <flux:input
                    wire:model.defer="slug.{{ $activeLocale }}"
                    label="Slug"
                    id="slug_{{ $activeLocale }}"
                />
                @error('slug.'.$activeLocale) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4"
        x-data="{
            state: {},
            autosaveTimer: null,
            startAutosaveTimer(interval) {
                this.stopAutosaveTimer();
                this.autosaveTimer = setInterval(() => {
                    $wire.set('state', this.state);
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
        x-on:block-is-editing.window="state = $event.detail.state"
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
                            @php($isEditingThisBlock = $editingBlock && $editingBlock->id === $block->id)
                            <div class="relative" @if(!$isEditingThisBlock) wire:click="editBlock({{ $block->id }})" @endif>
                                @php($blockClass = $this->blockManager->find($block->type))
                                @if($blockClass)
                                    <div class="@if(!$isEditingThisBlock) cursor-pointer hover:opacity-90 @endif transition-opacity">
                                        @include($blockClass->getFrontendView(), ['block' => $block, 'alpine' => $isEditingThisBlock])
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
                                @include($blockClass->getAdminView(), ['alpine' => true])
                            </div>
                        @endif

                        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button type="button" wire:click="cancelEdit" variant="subtle" size="sm">
                                Cancel
                            </flux:button>
                            <flux:button type="submit" @click="$wire.set('state', state)" variant="primary" size="sm">
                                Save changes
                            </flux:button>
                        </div>
                    </form>

                </div>
            @endif
        </div>
    </div>
</div>
