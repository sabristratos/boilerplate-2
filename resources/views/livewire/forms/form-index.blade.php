<div>
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="w-full md:w-1/3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('forms.search_placeholder') }}"
                icon="magnifying-glass"
                clearable
            />
        </div>

        <div class="flex items-center gap-2">
            <flux:select wire:model.live="perPage">
                <flux:select.option value="10">{{ __('forms.per_page', ['count' => 10]) }}</flux:select.option>
                <flux:select.option value="25">{{ __('forms.per_page', ['count' => 25]) }}</flux:select.option>
                <flux:select.option value="50">{{ __('forms.per_page', ['count' => 50]) }}</flux:select.option>
                <flux:select.option value="100">{{ __('forms.per_page', ['count' => 100]) }}</flux:select.option>
            </flux:select>
            <flux:button variant="primary" wire:click="create">{{ __('forms.new_form') }}</flux:button>
        </div>
    </div>

    <flux:table :paginate="$this->forms">
        <flux:table.columns>
            <flux:table.column>{{ __('forms.table_name') }}</flux:table.column>
            <flux:table.column>{{ __('forms.table_translations') }}</flux:table.column>
            <flux:table.column>{{ __('forms.table_submissions') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->forms as $form)
                <flux:table.row :key="$form->id">
                    <flux:table.cell>
                        <a href="{{ route('admin.forms.edit', ['form' => $form]) }}" wire:navigate>
                            <flux:text variant="strong">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:text>
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-x-2">
                            @foreach($this->locales as $code => $name)
                                <a href="{{ route('admin.forms.edit', ['form' => $form, 'locale' => $code]) }}" class="px-2 py-1 text-sm rounded-md {{ $form->getTranslation('title', $code, false) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}" wire:navigate>
                                    {{ strtoupper($code) }}
                                </a>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $form->formSubmissions()->count() }}
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button href="{{ route('admin.forms.submissions', $form) }}" variant="ghost" size="sm" icon="inbox-stack" square tooltip="{{ __('forms.submissions_tooltip') }}" wire:navigate/>
                        <flux:button
                            variant="danger"
                            size="sm"
                            icon="trash"
                            square
                            confirm
                            wire:click="deleteForm({{ $form->id }})"
                            tooltip="{{ __('forms.delete_form_tooltip') }}"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <div class="flex items-center justify-center p-12">
                            <flux:text variant="subtle">{{ __('forms.no_forms_found') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
