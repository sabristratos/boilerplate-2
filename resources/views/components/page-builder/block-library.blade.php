@props(['blockManager'])

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
    <div class="space-y-1.5">
        @forelse($this->filteredBlocks as $block)
            <div class="relative group">
                <button 
                    wire:click="createBlock('{{ $block->getType() }}')" 
                    type="button" 
                    class="w-full text-left p-2.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors group"
                >
                    <div class="flex items-center gap-2.5">
                        <div class="flex-shrink-0 w-7 h-7 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center group-hover:bg-zinc-200 dark:group-hover:bg-zinc-600 transition-colors">
                            <flux:icon name="{{ $block->getIcon() }}" class="w-3.5 h-3.5 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <div class="font-medium text-sm text-zinc-900 dark:text-white">
                                    {{ $block->getName() }}
                                </div>
                                <flux:badge size="xs" variant="subtle">{{ Str::title($block->getCategory()) }}</flux:badge>
                                <flux:badge size="xs" color="{{ $block->getComplexity() === 'basic' ? 'green' : ($block->getComplexity() === 'intermediate' ? 'yellow' : 'red') }}" variant="subtle">
                                    {{ Str::title($block->getComplexity()) }}
                                </flux:badge>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2">
                                {{ $block->getDescription() }}
                            </div>
                            @if(!empty($block->getTags()))
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach(array_slice($block->getTags(), 0, 2) as $tag)
                                        <span class="text-xs px-1 py-0.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 rounded">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($block->getTags()) > 2)
                                        <span class="text-xs px-1 py-0.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 rounded">
                                            +{{ count($block->getTags()) - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <flux:icon name="plus" class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors flex-shrink-0" />
                    </div>
                </button>
            </div>
        @empty
            <div class="text-center py-6">
                <flux:icon name="magnifying-glass" class="w-6 h-6 text-zinc-400 mx-auto mb-2" />
                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('messages.page_manager.no_blocks_found') }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">{{ __('messages.page_manager.try_adjusting_filters') }}</div>
            </div>
        @endforelse
    </div>
</div> 