<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('navigation.pages') }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item>{{ __('navigation.pages') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full md:w-1/3">
            <flux:autocomplete
                wire:model.live.debounce.300ms="search"
                :placeholder="__('labels.search_placeholder')"
                icon="magnifying-glass"
                clearable
            >
                @if($search && $this->pages->count() > 0)
                    @foreach($this->pages->take(5) as $page)
                        <flux:autocomplete.item value="{{ $page->getTranslation('title', app()->getLocale()) }}">
                            {{ $page->getTranslation('title', app()->getLocale()) }}
                        </flux:autocomplete.item>
                    @endforeach
                @endif
            </flux:autocomplete>
        </div>

        <div class="flex items-center gap-2">
            <flux:dropdown wire:model.live="showFiltersPopover">
                <flux:button
                    variant="outline"
                    icon="funnel"
                    icon:variant="micro"
                    icon:class="text-zinc-400"
                >
                    {{ __('buttons.filters') }}
                    @if (count(array_filter($this->filters)) > 0)
                        <flux:badge color="blue" size="sm">{{ count(array_filter($this->filters)) }}</flux:badge>
                    @endif
                </flux:button>
                <flux:popover class="w-80 space-y-4">
                    <flux:heading size="lg">{{ __('buttons.filters') }}</flux:heading>

                    <div class="space-y-4">
                        <flux:select
                            id="filter-locale"
                            wire:model.live="filters.locale"
                            label="{{ __('labels.locale') }}"
                        >
                            <flux:select.option value="">{{ __('labels.all_locales') }}</flux:select.option>
                            @foreach($this->locales as $key => $localeName)
                                <flux:select.option value="{{ $key }}">{{ $localeName }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:separator variant="subtle" />

                    <div class="flex justify-end">
                        <flux:button
                            wire:click="resetFilters"
                            variant="subtle"
                            size="sm"
                            class="justify-start -m-2 px-2!"
                        >
                            {{ __('buttons.reset_filters') }}
                        </flux:button>
                    </div>
                </flux:popover>
            </flux:dropdown>
            <flux:select wire:model.live="perPage">
                <flux:select.option value="10">{{ __('labels.per_page', ['count' => 10]) }}</flux:select.option>
                <flux:select.option value="25">{{ __('labels.per_page', ['count' => 25]) }}</flux:select.option>
                <flux:select.option value="50">{{ __('labels.per_page', ['count' => 50]) }}</flux:select.option>
                <flux:select.option value="100">{{ __('labels.per_page', ['count' => 100]) }}</flux:select.option>
            </flux:select>
            <flux:button
                href="{{ route('admin.import-export.index') }}?tab=export&type=pages"
                variant="outline"
                icon="arrow-down-tray"
            >
                {{ __('buttons.export') }}
            </flux:button>

            <flux:button wire:click="createPage" variant="primary" icon="plus" :tooltip="__('buttons.create_item', ['item' => __('navigation.pages')])">
                {{ __('buttons.create_item', ['item' => __('navigation.pages')]) }}
            </flux:button>
        </div>
    </div>
    <div class="rounded-lg overflow-hidden py-2">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Translations</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->pages as $page)
                    <tr wire:key="page-{{ $page->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $page->getTranslation('title', $filters['locale'] ?? app()->getLocale()) }}</div>
                            <div class="text-sm text-zinc-500">{{ $page->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @foreach($this->locales as $key => $localeName)
                                    <a href="{{ route('admin.pages.editor', ['page' => $page->id, 'locale' => $key]) }}" wire:navigate
                                       class="p-1 rounded-md {{ $page->hasTranslation($key) ? 'bg-green-100 text-green-800' : 'bg-zinc-100 text-zinc-800' }} hover:opacity-80 transition-opacity"
                                       title="{{ $localeName }}">
                                        {{ strtoupper($key) }}
                                    </a>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <flux:button
                                href="{{ route('pages.show', $page) }}"
                                target="_blank"
                                variant="ghost"
                                size="xs"
                                icon="eye"
                                square
                                tooltip="{{ __('buttons.view') }}"
                            />
                            <flux:button href="{{ route('admin.pages.editor', ['page' => $page->id, 'locale' => $filters['locale'] ?? app()->getLocale()]) }}" variant="ghost" size="xs" icon="pencil-square" square tooltip="{{ __('buttons.edit') }}" />
                            <x-revision-link :model="$page" model-type="page" />
                            <flux:button wire:click="confirmDelete({{ $page->id }})" variant="danger" size="xs" icon="trash" square tooltip="{{ __('buttons.delete') }}" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500">
                            {{ __('messages.no_pages_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->pages instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $this->pages->links() }}
        </div>
    @endif



    <flux:modal wire:model.live.self="showDeleteModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('messages.delete_confirm_title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('messages.delete_confirm_text') }}</flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button
                    wire:click="cancelDelete"
                    variant="outline"
                >
                    {{ __('buttons.cancel') }}
                </flux:button>
                <flux:button
                    wire:click="delete"
                    variant="danger"
                >
                    {{ __('buttons.delete') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>