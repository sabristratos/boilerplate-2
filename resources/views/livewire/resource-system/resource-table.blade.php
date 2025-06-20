<div>
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full md:w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search..."
                icon="magnifying-glass"
                clearable
            />
        </div>

        <div class="flex items-center gap-2">
            <flux:select
                wire:model.live="perPage"
                :disabled="!($resources instanceof \Illuminate\Pagination\AbstractPaginator)"
            >
                <flux:select.option value="10">{{ __('labels.per_page', ['count' => 10]) }}</flux:select.option>
                <flux:select.option value="25">{{ __('labels.per_page', ['count' => 25]) }}</flux:select.option>
                <flux:select.option value="50">{{ __('labels.per_page', ['count' => 50]) }}</flux:select.option>
                <flux:select.option value="100">{{ __('labels.per_page', ['count' => 100]) }}</flux:select.option>
            </flux:select>

            @if (count($availableFilters) > 0)
                <flux:button
                    variant="outline"
                    x-on:click="$wire.set('showFiltersModal', true)"
                >
                    {{ __('buttons.filters') }}
                    @if (count($this->filters) > 0)
                        <flux:badge color="blue" size="sm">{{ count($this->filters) }}</flux:badge>
                    @endif
                </flux:button>
            @endif
        </div>
    </div>

    {{-- Standard HTML Table for debugging --}}
    <div class="rounded-lg overflow-hidden py-2">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th scope="col" class="px-6 py-3 text-xs font-medium uppercase tracking-wider {{ $column->getAlignment() === 'center' ? 'text-center' : 'text-left' }} {{ $column->getAlignment() === 'right' ? 'text-right' : '' }}">
                            @if($column->isSortable())
                                <button wire:click="sort('{{ $column->getName() }}')" class="flex items-center gap-1 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                                    <span>{{ $column->getLabel() }}</span>
                                    @if($sortBy === $column->getName())
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            @else
                                <span class="text-zinc-500 dark:text-zinc-400">{{ $column->getLabel() }}</span>
                            @endif
                        </th>
                    @endforeach
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">{{ __('labels.actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody
                class="divide-y divide-zinc-200 dark:divide-zinc-700"
                wire:key="sortable-resource-table-body"
                x-data="{
                    reorder(event) {
                        const container = event.target;
                        const items = container.querySelectorAll('[x-sort\\:item]');
                        const ids = Array.from(items).map(item => item.getAttribute('x-sort:item'));
                        this.$wire.call('reorder', ids);
                    }
                }"
                x-sort
                x-on:sort.stop="reorder($event)"
            >
                @forelse ($resources as $resource)
                    <tr wire:key="resource-{{ $resource->id }}" x-sort:item="{{ $resource->id }}">
                        @foreach ($columns as $column)
                            <td class="px-6 py-2 whitespace-nowrap text-sm {{ $column->getAlignment() === 'center' ? 'text-center' : '' }} {{ $column->getAlignment() === 'right' ? 'text-right' : '' }}">
                                @if ($column->getName() === 'handle')
                                    <div class="flex items-center justify-center">
                                        <button x-sort:handle class="cursor-grab text-zinc-400 hover:text-zinc-600">
                                            <x-flux::icon.grip-vertical class="text-zinc-400 hover:text-zinc-600" />
                                        </button>
                                    </div>
                                @else
                                    @php
                                        $value = data_get($resource, $column->getName());
                                        $formattedValue = $column->formatValue($value, $resource);
                                        $name = data_get($resource, 'name');
                                    @endphp

                                    @if ($column instanceof \App\Services\ResourceSystem\Columns\BadgeColumn)
                                        <flux:badge
                                            color="{{ $formattedValue['color'] === 'success' ? 'green' : ($formattedValue['color'] === 'danger' ? 'red' : ($formattedValue['color'] === 'warning' ? 'yellow' : ($formattedValue['color'] === 'info' ? 'blue' : 'zinc'))) }}"
                                            size="sm"
                                        >
                                            {{ $formattedValue['value'] }}
                                        </flux:badge>
                                    @elseif ($column instanceof \App\Services\ResourceSystem\Columns\ImageColumn)
                                        <div class="flex items-center justify-center">
                                            @if($value)
                                                <flux:avatar src="{{ $value }}" size="sm" :circle="$column->isCircular()" />
                                            @else
                                                <flux:avatar :name="$name" size="sm" :circle="$column->isCircular()" />
                                            @endif
                                        </div>
                                    @elseif ($column instanceof \App\Services\ResourceSystem\Columns\RatingColumn)
                                        <div class="flex items-center {{ $column->getAlignment() === 'center' ? 'justify-center' : '' }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <flux:icon name="star" variant="{{ $i <= $value ? 'solid' : 'outline' }}" class="w-5 h-5 {{ $i <= $value ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" />
                                            @endfor
                                        </div>
                                    @else
                                        <span class="text-zinc-800 dark:text-zinc-200">{{ $formattedValue }}</span>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                             <flux:button href="{{ route('admin.resources.' . $this->resource::uriKey() . '.edit', $resource->id) }}" variant="ghost" size="xs" icon="pencil-square" square tooltip="{{ __('buttons.edit') }}" />
                             <flux:button wire:click="confirmDelete({{ $resource->id }})" variant="danger" size="xs" icon="trash" square tooltip="{{ __('buttons.delete') }}" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500">
                            {{ __('messages.no_resources_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($resources instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $resources->links() }}
        </div>
    @endif

    {{-- Modals --}}
    @if (count($availableFilters) > 0)
        <flux:modal
            wire:model.live.self="showFiltersModal"
            variant="flyout"
        >
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('buttons.filters') }}</flux:heading>

                <div class="space-y-4">
                    @foreach ($availableFilters as $filter)
                        @if ($filter instanceof \App\Services\ResourceSystem\Filters\SelectFilter)
                            <flux:select
                                id="filter-{{ $filter->getName() }}"
                                wire:model.live="filters.{{ $filter->getName() }}"
                                label="{{ $filter->getLabel() }}"
                            >
                                @foreach ($filter->getOptions() as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        @endif
                    @endforeach
                </div>

                <div class="flex justify-end gap-2">
                    <flux:button
                        wire:click="resetFilters"
                        variant="outline"
                    >
                        {{ __('buttons.reset_filters') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
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
