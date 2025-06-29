<div
    x-data="{
        liveState: null,
        editingBlockId: null,
    }"
    @block-state-updated.window="
        editingBlockId = $event.detail.id;
        liveState = $event.detail.state;
    "
    @block-was-updated.window="
        editingBlockId = null;
        liveState = null;
    "
    class="flex h-screen flex-col"
>

    <div class="flex-1 flex overflow-hidden">
        <main class="flex-1 overflow-y-auto p-6">
        <header class="bg-white dark:bg-zinc-800/50 backdrop-blur-md mb-6 z-20 shrink-0">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.pages.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ __('navigation.pages') }}</span>
                    </a>
                </div>

                @if(isset($page))
                    <div>
                        <h1 class="text-lg font-semibold text-zinc-900 dark:text-white truncate" title="{{ $page->getTranslation('title', app()->getLocale()) }}">
                            {{ $page->getTranslation('title', app()->getLocale()) }}
                        </h1>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    @if(count($this->availableLocales) > 1)
                            <flux:radio.group wire:model.live="switchLocale" variant="segmented" size="sm">
                                @foreach($this->availableLocales as $localeCode => $localeName)
                                    <flux:radio
                                        value="{{ $localeCode }}"
                                        label="{{ $localeName }}"
                                    >
                                    </flux:radio>
                                @endforeach
                            </flux:radio.group>
                    @endif
                </div>
            </div>
        </div>
    </header>
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
                                <flux:button wire:click="editBlock({{ $block->id }})" size="xs" variant="filled" icon="pencil-square"></flux:button>
                                <flux:button wire:click="deleteBlock({{ $block->id }})" size="xs" variant="danger" icon="trash"></flux:button>
                            </div>
                            <div class="p-2 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 flex items-center">
                                <div class="flex items-center gap-2">
                                    <div class="cursor-grab" x-sort:handle>
                                        <flux:icon name="bars-3" class="h-4 w-4 text-zinc-400"></flux:icon>
                                    </div>
                                    <flux:badge size="sm">{{ Str::title(str_replace('-', ' ', $block->type)) }}</flux:badge>
                                    @if($block->status)
                                        <flux:badge size="sm" color="{{ $block->status->color() }}" class="ml-2">
                                            {{ $block->status->label() }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div class="relative" wire:click.self="editBlock({{ $block->id }})">
                                @php
                                    $blockClass = $this->blockManager->find($block->type);
                                    $data = $block->data;
                                    $alpine = false;
                                    if ($editingBlockId === $block->id && $editingBlockState) {
                                        $data = array_merge($data, $editingBlockState);
                                        $alpine = true;
                                    }
                                @endphp

                                @if($blockClass)
                                    <div class="cursor-pointer hover:opacity-90 transition-opacity">
                                        @include($blockClass->getFrontendView(), ['block' => $block, 'data' => $data, 'alpine' => $alpine])
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center p-12 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                        <flux:icon name="layout-grid" class="h-10 w-10 text-zinc-400"></flux:icon>
                        <flux:text class="mt-4">{{ __('This page has no content yet.') }}</flux:text>
                        <flux:text variant="subtle">{{ __('Add a block from the library to get started.') }}</flux:text>
                    </div>
                @endforelse
            </div>
        </main>
        <aside class="w-[450px] bg-white dark:bg-zinc-800 border-l border-zinc-200 dark:border-zinc-700 overflow-y-auto shrink-0">
            <div class="p-6 space-y-6">
                <flux:tab.group>
                    <flux:tabs wire:model.live="activeSidebarTab" class="grid grid-cols-3">
                        <flux:tab name="settings" class="flex justify-center" icon="cog-6-tooth">
                            <flux:tooltip content="Page title, slug, and general settings">
                                Settings
                            </flux:tooltip>
                        </flux:tab>
                        <flux:tab name="add" class="flex justify-center"  icon="plus">
                            <flux:tooltip content="Add new content blocks to your page">
                                Add
                            </flux:tooltip>
                        </flux:tab>
                        <flux:tab name="edit" class="flex justify-center"  icon="pencil-square" :disabled="!$editingBlockId">
                            <flux:tooltip content="Edit the selected content block">
                                Edit
                            </flux:tooltip>
                        </flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="settings">
                        <form wire:submit.prevent="savePageDetails">
                            <div class="flex justify-between items-center mb-4">
                                <flux:heading size="sm">
                                    {{ __('messages.page_manager.edit_page') }}
                                </flux:heading>
                                <flux:button type="submit" variant="primary">{{ __('messages.page_manager.save_page') }}</flux:button>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <flux:input
                                        wire:model.defer="title.{{ $activeLocale }}"
                                        wire:change="generateSlug"
                                        label="{{ __('labels.title') }}"
                                        id="title_{{ $activeLocale }}"
                                    />
                                    @error('title.'.$activeLocale) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <flux:input
                                        wire:model.defer="slug"
                                        label="{{ __('labels.slug') }}"
                                        id="slug"
                                    />
                                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </form>
                    </flux:tab.panel>

                    <flux:tab.panel name="add">
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('messages.page_manager.block_library') }}</h3>
                            <div class="space-y-1 p-1">
                                @foreach($this->blockManager->getAvailableBlocks() as $block)
                                    <button wire:click="createBlock('{{ $block->getType() }}')" type="button" class="w-full text-left p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 text-sm">
                                        <div class="flex items-center">
                                            <flux:icon name="{{ $block->getIcon() }}" class="h-4 w-4 mr-2"></flux:icon>
                                            {{ $block->getName() }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="edit">
                        <livewire:admin.block-editor :state="$editingBlockState" :active-locale="$activeLocale" />
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </aside>
    </div>
</div>
