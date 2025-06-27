<div>
    <flux:toast />
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div>
                <flux:heading size="xl" class="mb-2">{{ __('translations.title') }}</flux:heading>
                <flux:text>{{ __('translations.description') }}</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <input type="file" wire:model="upload" class="hidden" id="import-file-input" />
                <flux:button wire:click="scan">{{ __('translations.scan_button') }}</flux:button>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">{{ __('translations.actions_button') }}</flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="export">{{ __('translations.export_button') }}</flux:menu.item>
                        <flux:menu.item onclick="document.getElementById('import-file-input').click()">{{ __('translations.import_button') }}</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="resetLocales">{{ __('buttons.cancel') }}</flux:button>
                    <flux:button variant="primary" wire:click.prevent="save">{{ __('buttons.save') }}</flux:button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="w-full md:w-1/3">
                    <flux:input
                        wire:model.live.debounce.300ms="searchQuery"
                        :placeholder="__('translations.search_by_key_or_text')"
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <div class="flex items-center gap-2">
                    <flux:select
                        wire:model.live="filterGroup"
                        clearable
                    >
                        <flux:select.option value="">{{ __('translations.all_groups') }}</flux:select.option>
                        @foreach($groups as $group)
                            <flux:select.option value="{{ $group }}">{{ $group }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select
                        wire:model.live="selectedLocales"
                        placeholder="{{ __('translations.filter_by_locales') }}"
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
                        <flux:select.option value="10">{{ __('translations.per_page', ['count' => 10]) }}</flux:select.option>
                        <flux:select.option value="25">{{ __('translations.per_page', ['count' => 25]) }}</flux:select.option>
                        <flux:select.option value="50">{{ __('translations.per_page', ['count' => 50]) }}</flux:select.option>
                        <flux:select.option value="100">{{ __('translations.per_page', ['count' => 100]) }}</flux:select.option>
                    </flux:select>
                    <flux:button wire:click="resetFilters" variant="ghost">{{ __('translations.reset_filters_button') }}</flux:button>
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
                    {{ __('translations.group_column') }}
                </flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'key'"
                    :direction="$sortDirection"
                    wire:click="sort('key')"
                >
                    {{ __('translations.key_column') }}
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
                                    placeholder="{{ __('translations.no_translation_placeholder') }}"
                                />
                            </flux:table.cell>
                        @endforeach
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ count($selectedLocales) + 2 }}" class="text-center text-slate-500 py-8">
                            {{ __('translations.no_translations_found') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
