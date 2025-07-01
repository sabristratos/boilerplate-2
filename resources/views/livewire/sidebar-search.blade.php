<div class="relative" x-data="{ 
    open: false,
    selectedIndex: -1,
    handleKeydown(event) {
        if (!this.open) return;
        
        switch(event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, {{ $searchResults->count() }} - 1);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                break;
            case 'Enter':
                event.preventDefault();
                if (this.selectedIndex >= 0) {
                    $wire.selectItem(this.selectedIndex);
                }
                break;
            case 'Escape':
                event.preventDefault();
                $wire.clearSearch();
                this.open = false;
                break;
        }
    },
    handleClickOutside(event) {
        if (!this.$el.contains(event.target)) {
            this.open = false;
            $wire.clearSearch();
        }
    }
}" @keydown.window="handleKeydown($event)" @click.away="handleClickOutside($event)">
    
    <flux:autocomplete 
        wire:model.live.debounce.300ms="search"
        :placeholder="__('navigation.search.placeholder')"
        icon="magnifying-glass"
        clearable
        kbd="Ctrl+K"
        @focus="open = true"
        @input="open = true"
        class="w-full"
    >
        @if($search && $searchResults->isNotEmpty())
            @foreach($searchResults as $index => $result)
                <flux:autocomplete.item 
                    value="{{ $result['label'] }}"
                    @click="$wire.selectItem({{ $index }})"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedIndex === {{ $index }} }"
                    class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                    <div class="flex items-center gap-3 flex-1">
                        <flux:icon name="{{ $result['icon'] }}" class="w-4 h-4 text-gray-500" />
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['label'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $result['description'] }}
                            </div>
                        </div>
                        @if($result['external'])
                            <flux:icon name="arrow-top-right-on-square" class="w-3 h-3 text-gray-400" />
                        @endif
                    </div>
                </flux:autocomplete.item>
            @endforeach
        @endif
    </flux:autocomplete>

    <!-- Search Results Dropdown -->
    @if($showResults && $searchResults->isNotEmpty())
        <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
            <div class="py-1">
                @foreach($searchResults as $index => $result)
                    <button
                        type="button"
                        wire:click="selectItem({{ $index }})"
                        class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 focus:outline-none"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedIndex === {{ $index }} }"
                    >
                        <flux:icon name="{{ $result['icon'] }}" class="w-4 h-4 text-gray-500 flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['label'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $result['description'] }}
                            </div>
                        </div>
                        @if($result['external'])
                            <flux:icon name="arrow-top-right-on-square" class="w-3 h-3 text-gray-400 flex-shrink-0" />
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- No Results Message -->
    @if($search && $searchResults->isEmpty() && strlen($search) >= 2)
        <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50">
            <div class="px-3 py-4 text-center">
                <flux:icon name="magnifying-glass" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('navigation.no_results_found_for') }} "<span class="font-medium">{{ $search }}</span>"
                </div>
                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ __('navigation.try_searching_something_else') }}
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('navigate-to', (event) => {
        window.location.href = event.url;
    });

    Livewire.on('open-external-link', (event) => {
        window.open(event.url, '_blank');
    });
});
</script> 