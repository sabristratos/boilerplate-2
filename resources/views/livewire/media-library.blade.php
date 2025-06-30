<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <flux:heading>{{ __('media.title') }}</flux:heading>
                <div class="flex items-center space-x-4">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        :placeholder="__('media.search_placeholder')"
                        icon="magnifying-glass"
                        clearable
                    />
                </div>
            </div>

            <div class="mt-6">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-2">
                            <flux:button
                                wire:click="sortBy('created_at')"
                                variant="{{ $sortField === 'created_at' ? 'primary' : 'outline' }}"
                                size="sm"
                                :icon="$sortField === 'created_at' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : null"
                            >
                                {{ __('media.sort_by_date') }}
                            </flux:button>
                            <flux:button
                                wire:click="sortBy('name')"
                                variant="{{ $sortField === 'name' ? 'primary' : 'outline' }}"
                                size="sm"
                                :icon="$sortField === 'name' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : null"

                            >
                                {{ __('media.sort_by_name') }}
                            </flux:button>
                            <flux:button
                                wire:click="sortBy('size')"
                                variant="{{ $sortField === 'size' ? 'primary' : 'outline' }}"
                                size="sm"
                                :icon="$sortField === 'size' ? ($sortDirection === 'asc' ? 'arrow-up' : 'arrow-down') : null"

                            >
                                {{ __('media.sort_by_size') }}
                            </flux:button>
                        </div>
                        <div>
                            <flux:select wire:model.live="perPage" size="sm">
                                <flux:select.option value="12">{{ __('media.per_page', ['count' => 12]) }}</flux:select.option>
                                <flux:select.option value="24">{{ __('media.per_page', ['count' => 24]) }}</flux:select.option>
                                <flux:select.option value="48">{{ __('media.per_page', ['count' => 48]) }}</flux:select.option>
                            </flux:select>
                        </div>
                    </div>

                    @if($media->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($media as $item)
                                <div class="group relative">
                                    <a href="{{ route('admin.media.show', $item->id) }}" class="block">
                                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                            @if(str_contains($item->mime_type, 'image'))
                                                <img
                                                    src="{{ $item->getUrl() }}"
                                                    alt="{{ $item->name }}"
                                                    class="h-full w-full object-cover object-center"
                                                >
                                            @else
                                                <div class="flex h-full w-full items-center justify-center">
                                                    <flux:icon name="{{ $this->getIconForMimeType($item->mime_type) }}" class="h-16 w-16 text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $this->formatFileSize($item->size) }} • {{ $item->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </a>
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:dropdown align="end">
                                            <flux:button variant="ghost" size="xs" icon="ellipsis-vertical" />
                                            <flux:menu>
                                                <flux:menu.item icon="eye" href="{{ route('admin.media.show', $item->id) }}">
                                                    {{ __('media.view_details') }}
                                                </flux:menu.item>
                                                <flux:menu.item
                                                    icon="trash"
                                                    variant="danger"
                                                    wire:click="deleteMedia({{ $item->id }})"
                                                    :wire:confirm="__('media.delete_confirm')"
                                                >
                                                    {{ __('media.delete') }}
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $media->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <flux:icon name="photo" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('media.no_media_found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($search)
                                    {{ __('media.no_media_matching_search', ['search' => $search]) }}
                                @else
                                    {{ __('media.get_started') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
