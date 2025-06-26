<div>
    <div class="flex items-center justify-between">
        <flux:heading level="1">{{ __('forms.submissions_for', ['name' => $form->name]) }}</flux:heading>
        <flux:button href="{{ route('admin.forms.index') }}" wire:navigate variant="ghost">
            {{ __('forms.back_to_forms') }}
        </flux:button>
    </div>

    <div class="mt-4">
        <x-flux::table>
            <x-slot:header>
                <x-flux::table.h>{{ __('forms.table_submitted_at') }}</x-flux::table.h>
                <x-flux::table.h></x-flux::table.h>
            </x-slot:header>
            <x-slot:body>
                @forelse($submissions as $submission)
                    <x-flux::table.row>
                        <x-flux::table.cell>
                            {{ $submission->created_at->format('Y-m-d H:i') }}
                        </x-flux::table.cell>
                        <x-flux::table.cell class="text-right">
                            <flux:modal.trigger :name="'submission-details-'.$submission->id">
                                <flux:button size="sm">
                                    {{ __('forms.view') }}
                                </flux:button>
                            </flux:modal.trigger>
                        </x-flux::table.cell>
                    </x-flux::table.row>
                @empty
                    <x-flux::table.row>
                        <x-flux::table.cell colspan="2" class="text-center">
                            <flux:text variant="subtle">{{ __('forms.no_submissions_found') }}</flux:text>
                        </x-flux::table.cell>
                    </x-flux::table.row>
                @endforelse
            </x-slot:body>
        </x-flux::table>
    </div>

    @foreach($submissions as $submission)
        <flux:modal :name="'submission-details-'.$submission->id" :title="__('forms.submission_details')">
            <div class="space-y-4">
                @foreach($submission->data as $key => $value)
                    <div>
                        <dt class="font-medium">{{ $key }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>
            <x-slot:footer>
                <flux:modal.close>
                    <flux:button>
                        {{ __('forms.close') }}
                    </flux:button>
                </flux:modal.close>
            </x-slot:footer>
        </flux:modal>
    @endforeach
</div>
