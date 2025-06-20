<div>
    <flux:toast />
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div>
                <flux:heading size="xl" class="mb-2">Translations</flux:heading>
                <flux:text>Manage your application's translation strings across all languages.</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <input type="file" wire:model="upload" class="hidden" id="import-file-input" />
                <flux:button wire:click="scan">Scan</flux:button>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">Actions</flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="export">Export</flux:menu.item>
                        <flux:menu.item onclick="document.getElementById('import-file-input').click()">Import</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
                <flux:button variant="primary" wire:click="save">Save</flux:button>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-1/3">
                    <flux:input
                        wire:model.live.debounce.300ms="searchQuery"
                        placeholder="Search by key or text..."
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <div class="flex items-center gap-2">
                    <flux:select
                        wire:model.live="filterGroup"
                        placeholder="Filter by group"
                        clearable
                    >
                        @foreach($groups as $group)
                            <flux:select.option value="{{ $group }}">{{ $group }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select
                        wire:model.live="selectedLocales"
                        placeholder="Filter by locales"
                        variant="listbox"
                        multiple
                    >
                        @foreach($locales as $locale)
                            <flux:select.option value="{{ $locale }}">{{ strtoupper($locale) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select
                        wire:model.live="perPage"
                    >
                        <flux:select.option value="10">10 per page</flux:select.option>
                        <flux:select.option value="25">25 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                    <flux:button wire:click="resetFilters" variant="ghost">Reset Filters</flux:button>
                </div>
            </div>
        </div>

        <!-- Translations table -->
        <flux:table :paginate="$translations">
            <flux:table.columns>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'group'"
                    :direction="$sortDirection"
                    wire:click="sort('group')"
                >
                    Group
                </flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'key'"
                    :direction="$sortDirection"
                    wire:click="sort('key')"
                >
                    Key
                </flux:table.column>
                @foreach($selectedLocales as $locale)
                    <flux:table.column>
                        {{ strtoupper($locale) }}
                    </flux:table.column>
                @endforeach
            </flux:table.columns>
            <flux:table.rows>
                @forelse($translations as $translation)
                    <flux:table.row wire:key="translation-{{ $translation->id }}">
                        <flux:table.cell class="align-top max-w-sm truncate">
                            <flux:text variant="strong" title="{{ $translation->group }}">{{ $translation->group }}</flux:text>
                        </flux:table.cell>
                        <flux:table.cell class="align-top max-w-sm truncate">
                            <flux:text variant="strong" title="{{ $translation->key }}">{{ $translation->key }}</flux:text>
                        </flux:table.cell>
                        @foreach($selectedLocales as $locale)
                            <flux:table.cell class="align-top p-2">
                                <flux:textarea
                                    wire:model.defer="translationsData.{{ $translation->id }}.{{ $locale }}"
                                    rows="auto"
                                    class:input="min-h-[40px] max-w-2xl w-full"
                                    placeholder="No translation"
                                />
                            </flux:table.cell>
                        @endforeach
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ count($selectedLocales) + 2 }}" class="text-center text-slate-500 py-8">
                            No translations found. Try adjusting your filters.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
