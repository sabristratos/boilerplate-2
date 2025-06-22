<div>
    <div class="mt-4">
        {{-- Current Media Preview --}}
        @if($mediaUrl)
            <div class="mb-4">
                <div class="relative w-full">
                    <div class="w-full aspect-video overflow-hidden rounded-md border border-zinc-200 dark:border-zinc-700">
                        <img src="{{ $mediaUrl }}" alt="{{ $model->name ?? 'Media' }}" class="w-full h-full object-cover object-center">
                    </div>
                    <div class="absolute top-2 right-2">
                        <flux:button wire:click="remove" variant="danger" size="xs" icon="trash">
                            <span class="hidden sm:inline">{{ __('buttons.remove') }}</span>
                        </flux:button>
                    </div>
                </div>
                <div class="mt-2">
                    <flux:callout icon="exclamation-triangle" variant="warning" class="text-sm">
                        <flux:callout.text>
                            {{ __('media.remove_media_warning') }}
                        </flux:callout.text>
                    </flux:callout>
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-2">
            <flux:button x-on:click="$wire.showUploadModal = true" icon="arrow-up-tray" size="sm" class="flex-grow sm:flex-grow-0">
                {{ __('buttons.upload') }}
            </flux:button>
            <flux:button x-on:click="$wire.showUrlModal = true" icon="link" size="sm" class="flex-grow sm:flex-grow-0">
                {{ __('buttons.add_from_url') }}
            </flux:button>
            <flux:button x-on:click="$wire.showExistingMediaModal = true" icon="photo" size="sm" class="flex-grow sm:flex-grow-0">
                {{ __('buttons.select_existing') }}
            </flux:button>
        </div>

        {{-- Upload Modal --}}
        <flux:modal wire:model.self="showUploadModal" class="md:w-96">
            <flux:heading>{{ __('media.upload_media') }}</flux:heading>
            <div class="mt-4">
                <flux:input
                    type="file"
                    wire:model.live="file"
                    accept="image/*"
                />
                @error('file') <div class="mt-1 text-red-500 text-sm">{{ $message }}</div> @enderror

                {{-- File Preview --}}
                @if($file)
                    <div class="mt-4">
                        <div class="w-full aspect-video overflow-hidden rounded-md border border-zinc-200 dark:border-zinc-700">
                            <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover object-center">
                        </div>
                    </div>
                @endif
            </div>
            <div class="pt-4 mt-4 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                <flux:button x-on:click="$wire.showUploadModal = false" variant="ghost">
                    {{ __('buttons.cancel') }}
                </flux:button>
                <flux:button wire:click="save" variant="primary">
                    {{ __('buttons.upload') }}
                </flux:button>
            </div>
        </flux:modal>

        {{-- URL Modal --}}
        <flux:modal wire:model.self="showUrlModal" class="md:w-96">
            <flux:heading>{{ __('media.add_from_url') }}</flux:heading>
            <div class="mt-4">
                <flux:input
                    wire:model.live="url"
                    :placeholder="__('labels.url_placeholder')"
                />
                @error('url') <div class="mt-1 text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>
            <div class="pt-4 mt-4 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                <flux:button x-on:click="$wire.showUrlModal = false" variant="ghost">
                    {{ __('buttons.cancel') }}
                </flux:button>
                <flux:button wire:click="save" variant="primary">
                    {{ __('buttons.save') }}
                </flux:button>
            </div>
        </flux:modal>

        {{-- Select Existing Media Modal --}}
        <flux:modal wire:model.self="showExistingMediaModal" class="md:max-w-3xl">
            <flux:heading>{{ __('media.select_existing_media') }}</flux:heading>
            <div class="mt-4">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('labels.search_media')"
                    icon="magnifying-glass"
                    clearable
                />

                <div class="mt-3">
                    <flux:callout icon="information-circle" variant="outline" class="text-sm">
                        <flux:callout.text>
                            {{ __('media.select_existing_media_warning') }}
                        </flux:callout.text>
                    </flux:callout>
                </div>

                @if($existingMedia->count() > 0)
                    <flux:checkbox.group :label="__('labels.select_media')" variant="cards" class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($existingMedia as $item)
                            <flux:checkbox wire:click="toggleMediaSelection({{ $item->id }})" value="{{ $item->id }}" :checked="in_array($item->id, $selectedMediaIds)" class="relative">
                                <div class="absolute top-2 left-2 z-10">
                                    <flux:checkbox.indicator />
                                </div>

                                <div class="flex-1">
                                    <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800 rounded-lg mb-2">
                                        <img
                                            src="{{ $item->getUrl() }}"
                                            alt="{{ $item->name }}"
                                            class="h-full w-full object-cover object-center"
                                        >
                                    </div>
                                    <flux:heading class="leading-4 text-sm">{{ $item->name }}</flux:heading>
                                    <flux:text size="sm" class="mt-1 text-xs text-gray-500">{{ $item->mime_type }}</flux:text>
                                </div>
                            </flux:checkbox>
                        @endforeach
                    </flux:checkbox.group>

                    <div class="mt-6">
                        {{ $existingMedia->links() }}
                    </div>
                @else
                    <div class="mt-4 text-center py-8">
                        <flux:icon name="photo" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('media.no_media_found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($search)
                                {{ __('media.no_media_matching_search', ['search' => $search]) }}
                            @else
                                {{ __('media.upload_some_media_first') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
            <div class="pt-4 mt-4 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                <flux:button x-on:click="$wire.showExistingMediaModal = false" variant="ghost">
                    {{ __('buttons.cancel') }}
                </flux:button>
                <flux:button wire:click="confirmMediaSelection" variant="primary">
                    {{ __('buttons.select') }}
                </flux:button>
            </div>
        </flux:modal>
    </div>
</div>
