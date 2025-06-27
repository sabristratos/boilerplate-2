<div>
    <div class="flex items-center justify-between">
        <flux:heading level="1">{{ __('forms.submissions_for', ['name' => $form->name]) }}</flux:heading>
        <flux:button href="{{ route('admin.forms.index') }}" wire:navigate variant="ghost">
            {{ __('forms.back_to_forms') }}
        </flux:button>
    </div>

    <div class="mt-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('forms.table_submitted_at') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($submissions as $submission)
                    <flux:table.row>
                        <flux:table.cell>
                            {{ $submission->created_at->format('Y-m-d H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:modal.trigger :name="'submission-details-'.$submission->id">
                                <flux:button size="sm">
                                    {{ __('forms.view') }}
                                </flux:button>
                            </flux:modal.trigger>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="2">
                            <div class="flex flex-col items-center justify-center py-12">
                                <flux:icon name="inbox" class="h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('forms.no_submissions_found') }}</h3>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @foreach($submissions as $submission)
        <flux:modal :name="'submission-details-'.$submission->id" :title="__('forms.submission_details')">
            <div class="space-y-4">
                @foreach($submission->data as $key => $value)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <dt class="font-medium text-gray-500">{{ Str::title(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 md:col-span-2">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>

            <x-slot:footer>
                <flux:button variant="ghost" x-on:click="$flux.modal('submission-details-{{ $submission->id }}').close()">
                    {{ __('forms.close') }}
                </flux:button>
            </x-slot:footer>
        </flux:modal>
    @endforeach
</div>
