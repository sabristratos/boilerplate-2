<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('navigation.forms') }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item>{{ __('navigation.forms') }}</flux:breadcrumbs.item>
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
                @if($search && $forms->count() > 0)
                    @foreach($forms->take(5) as $form)
                        <flux:autocomplete.item value="{{ $form->getTranslation('name', app()->getLocale()) }}">
                            {{ $form->getTranslation('name', app()->getLocale()) }}
                        </flux:autocomplete.item>
                    @endforeach
                @endif
            </flux:autocomplete>
        </div>

        <div class="flex items-center gap-2">
            <flux:select
                wire:model.live="perPage"
                :disabled="!($forms instanceof \Illuminate\Pagination\AbstractPaginator)"
            >
                <flux:select.option value="10">{{ __('labels.per_page', ['count' => 10]) }}</flux:select.option>
                <flux:select.option value="25">{{ __('labels.per_page', ['count' => 25]) }}</flux:select.option>
                <flux:select.option value="50">{{ __('labels.per_page', ['count' => 50]) }}</flux:select.option>
                <flux:select.option value="100">{{ __('labels.per_page', ['count' => 100]) }}</flux:select.option>
            </flux:select>

            <flux:button
                href="{{ route('admin.import-export.index') }}?tab=export&type=forms"
                variant="outline"
                icon="arrow-down-tray"
            >
                {{ __('buttons.export') }}
            </flux:button>

            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('buttons.create_item', ['item' => __('navigation.forms')]) }}
            </flux:button>
        </div>
    </div>

    <div class="rounded-lg overflow-hidden py-2">
        <flux:table :paginate="$forms">
            <flux:table.columns>
                <flux:table.column>{{ __('labels.name') }}</flux:table.column>
                <flux:table.column>{{ __('labels.elements') }}</flux:table.column>
                <flux:table.column>{{ __('labels.submissions') }}</flux:table.column>
                <flux:table.column>{{ __('labels.status') }}</flux:table.column>
                <flux:table.column>{{ __('labels.created_at') }}</flux:table.column>
                <flux:table.column align="end">{{ __('labels.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($forms as $form)
                    <flux:table.row :key="$form->id">
                        <flux:table.cell>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $form->getTranslation('name', app()->getLocale()) }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $form->elements ? count($form->elements) : 0 }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $form->submissions()->count() }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                                $formStatus = $this->getFormStatusForForm($form);
                            @endphp
                            <flux:badge :color="$formStatus->getColor()" size="sm" :icon="$formStatus->getIcon()">
                                {{ $formStatus->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $form->created_at->format('M j, Y') }}
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button icon="ellipsis-horizontal" variant="ghost" size="xs" square :tooltip="__('buttons.actions')" />
                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" href="{{ route('admin.forms.edit', $form) }}">
                                        {{ __('buttons.edit') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="document-text" href="{{ route('admin.forms.submissions', $form) }}">
                                        {{ __('messages.forms.tooltips.submissions') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="document-duplicate" wire:click.prevent="duplicateForm({{ $form->id }})">
                                        {{ __('forms.buttons.duplicate') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="clock" href="{{ route('admin.revisions.show', ['modelType' => 'form', 'modelId' => $form->id]) }}">
                                        {{ __('revisions.view_history') }}
                                    </flux:menu.item>
                                    <flux:menu.item icon="eye" href="#">
                                        {{ __('buttons.view') }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" variant="danger" wire:click.prevent="confirmDelete({{ $form->id }})">
                                        {{ __('buttons.delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" align="center">
                            {{ __('messages.no_forms_found') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @if ($forms instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $forms->links() }}
        </div>
    @endif

    <flux:modal name="create-form">
        <form wire:submit.prevent="create">
            <flux:heading>{{ __('messages.forms.form_builder_interface.create_new_form') }}</flux:heading>
            <div class="mt-4">
                <flux:select wire:model="selectedPrebuiltForm" :label="__('messages.forms.form_builder_interface.prebuilt_form_optional')" :placeholder="__('messages.forms.form_builder_interface.choose_prebuilt_form')">
                    <flux:select.option value="">{{ __('messages.forms.form_builder_interface.none') }}</flux:select.option>
                    @foreach($this->availablePrebuiltForms as $prebuilt)
                        <flux:select.option value="{{ get_class($prebuilt) }}">{{ $prebuilt->getName() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="mt-4">
                <flux:input wire:model="newFormName" :label="__('messages.forms.form_builder_interface.form_name')" :placeholder="__('messages.forms.form_builder_interface.form_name_placeholder')" />
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button variant="ghost" @click="$flux.modal('create-form').close()">{{ __('messages.forms.form_builder_interface.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="plus">{{ __('messages.forms.form_builder_interface.create') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
