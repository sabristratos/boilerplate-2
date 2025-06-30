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

            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('buttons.create_item', ['item' => __('navigation.forms')]) }}
            </flux:button>
        </div>
    </div>

    <div class="rounded-lg overflow-hidden py-2">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('labels.name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('labels.elements') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('labels.submissions') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('labels.status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('labels.created_at') }}
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">{{ __('labels.actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($forms as $form)
                    <tr wire:key="form-{{ $form->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $form->getTranslation('name', app()->getLocale()) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $form->elements ? count($form->elements) : 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $form->submissions()->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge color="green" size="sm">
                                {{ $form->status ?? __('labels.draft') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $form->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <flux:button
                                href="{{ route('admin.forms.edit', $form) }}"
                                variant="ghost"
                                size="xs"
                                icon="pencil-square"
                                square
                                tooltip="{{ __('buttons.edit') }}"
                                wire:navigate
                            />
                            <flux:button
                                href="{{ route('admin.forms.submissions', $form) }}"
                                variant="ghost"
                                size="xs"
                                icon="document-text"
                                square
                                tooltip="{{ __('buttons.view_submissions') }}"
                                wire:navigate
                            />
                            <flux:button
                                href="#"
                                variant="ghost"
                                size="xs"
                                icon="eye"
                                square
                                tooltip="{{ __('buttons.view') }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500">
                            {{ __('messages.no_forms_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($forms instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $forms->links() }}
        </div>
    @endif

    <flux:modal name="create-form">
        <form wire:submit.prevent="create">
            <flux:heading>Create a new form</flux:heading>
            <div class="mt-4">
                <flux:select wire:model="selectedPrebuiltForm" label="Prebuilt Form (optional)" placeholder="Choose a prebuilt form...">
                    <flux:select.option value="">-- None --</flux:select.option>
                    @foreach($this->availablePrebuiltForms as $prebuilt)
                        <flux:select.option value="{{ get_class($prebuilt) }}">{{ $prebuilt->getName() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="mt-4">
                <flux:input wire:model="newFormName" label="Form Name" placeholder="e.g. Contact Us" />
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <flux:button variant="ghost" @click="$flux.modal('create-form').close()">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
