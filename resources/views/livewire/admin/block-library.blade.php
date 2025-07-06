<div class="space-y-3">
    <div>
        <flux:heading size="sm">{{ __('messages.page_manager.block_library') }}</flux:heading>
        <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.add_content_blocks') }}</flux:text>
    </div>
    
    <!-- Search -->
    <flux:input 
        wire:model.live="blockSearch" 
        placeholder="{{ __('messages.page_manager.search_blocks') }}" 
        icon="magnifying-glass"
        size="sm"
    />
    
    <!-- Filters -->
    <div class="space-y-2">
        <flux:select wire:model.live="selectedCategory" size="sm">
            <flux:select.option value="">{{ __('messages.page_manager.all_categories') }}</flux:select.option>
            @foreach($this->availableCategories as $category)
                <flux:select.option value="{{ $category }}">{{ Str::title($category) }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:radio.group wire:model.live="selectedComplexity" variant="segmented" size="sm">
            <flux:radio value="" label="{{ __('messages.page_manager.all_complexities') }}" />
            <flux:radio value="basic" label="{{ __('messages.page_manager.basic') }}" />
            <flux:radio value="intermediate" label="{{ __('messages.page_manager.intermediate') }}" />
            <flux:radio value="advanced" label="{{ __('messages.page_manager.advanced') }}" />
        </flux:radio.group>
    </div>
    
    <!-- Block List -->
    <div class="space-y-2 max-h-[calc(100vh-400px)] overflow-y-auto">
        @forelse($this->filteredBlocks as $block)
            <div class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors cursor-pointer group" wire:click="createBlock('{{ $block->getType() }}')">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center group-hover:bg-zinc-200 dark:group-hover:bg-zinc-700 transition-colors">
                        <flux:icon name="{{ $block->getIcon() }}" class="w-4 h-4 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <flux:text class="text-sm font-medium text-zinc-900 dark:text-white truncate">
                            {{ $block->getName() }}
                        </flux:text>
                        <flux:text variant="subtle" class="text-xs line-clamp-2">
                            {{ $block->getDescription() }}
                        </flux:text>
                        <div class="flex items-center gap-2 mt-1">
                            <flux:badge size="sm" variant="subtle" color="zinc">
                                {{ Str::title($block->getCategory()) }}
                            </flux:badge>
                            <flux:badge size="sm" variant="subtle" color="blue">
                                {{ Str::title($block->getComplexity()) }}
                            </flux:badge>
                        </div>
                    </div>
                    <flux:icon name="plus" class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors" />
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <flux:icon name="magnifying-glass" class="w-8 h-8 text-zinc-400 mx-auto mb-2" />
                <flux:text variant="subtle" class="text-sm">{{ __('messages.page_manager.no_blocks_found') }}</flux:text>
            </div>
        @endforelse
    </div>
</div> 