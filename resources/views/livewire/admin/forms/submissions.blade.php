<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('forms.submissions_for', ['name' => $form->getTranslation('name', app()->getLocale())]) }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item href="{{ route('admin.forms.index') }}">{{ __('navigation.forms') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('admin.forms.edit', $form) }}">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('labels.submissions') }}</flux:breadcrumbs.item>
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
                @if($search && $submissions->count() > 0)
                    @foreach($submissions->take(5) as $submission)
                        <flux:autocomplete.item value="{{ $submission->ip_address }}">
                            {{ $submission->ip_address }} - {{ $submission->created_at->format('M j, Y') }}
                        </flux:autocomplete.item>
                    @endforeach
                @endif
            </flux:autocomplete>
        </div>

        <div class="flex items-center gap-2">
            <flux:select
                wire:model.live="perPage"
                :disabled="!($submissions instanceof \Illuminate\Pagination\AbstractPaginator)"
            >
                <flux:select.option value="10">{{ __('labels.per_page', ['count' => 10]) }}</flux:select.option>
                <flux:select.option value="25">{{ __('labels.per_page', ['count' => 25]) }}</flux:select.option>
                <flux:select.option value="50">{{ __('labels.per_page', ['count' => 50]) }}</flux:select.option>
                <flux:select.option value="100">{{ __('labels.per_page', ['count' => 100]) }}</flux:select.option>
            </flux:select>

            <flux:button href="{{ route('admin.forms.edit', $form) }}" variant="ghost" icon="arrow-left">
                {{ __('buttons.back_to_form') }}
            </flux:button>
        </div>
    </div>

    <div class="rounded-lg overflow-hidden py-2">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider cursor-pointer" wire:click="sort('created_at')">
                        <div class="flex items-center gap-2">
                            {{ __('forms.table_submitted_at') }}
                            @if($sortBy === 'created_at')
                                <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider cursor-pointer" wire:click="sort('ip_address')">
                        <div class="flex items-center gap-2">
                            {{ __('forms.table_ip_address') }}
                            @if($sortBy === 'ip_address')
                                <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        {{ __('forms.table_data_preview') }}
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">{{ __('labels.actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($submissions as $submission)
                    <tr wire:key="submission-{{ $submission->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $submission->created_at->format('M j, Y') }}</div>
                            <div class="text-sm text-zinc-500">{{ $submission->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-zinc-900 dark:text-white">{{ $submission->ip_address }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900 dark:text-white max-w-xs truncate">
                                @if(is_array($submission->data))
                                    @foreach(array_slice($submission->data, 0, 2) as $key => $value)
                                        <div class="truncate">
                                            <span class="font-medium">{{ $key }}:</span> 
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ is_string($value) ? \Illuminate\Support\Str::limit($value, 30) : json_encode($value) }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($submission->data) > 2)
                                        <div class="text-xs text-zinc-500">+{{ count($submission->data) - 2 }} more fields</div>
                                    @endif
                                @else
                                    <span class="text-zinc-500">{{ __('forms.no_data') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <flux:button
                                href="{{ route('admin.forms.submissions.show', ['form' => $form, 'submission' => $submission]) }}"
                                variant="ghost"
                                size="xs"
                                icon="eye"
                                square
                                tooltip="{{ __('buttons.view_details') }}"
                                wire:navigate
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-zinc-500">
                            @if($search)
                                {{ __('forms.no_submissions_found') }}
                            @else
                                {{ __('messages.forms.no_submissions_yet') }}
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($submissions instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="mt-4">
            {{ $submissions->links() }}
        </div>
    @endif
</div> 