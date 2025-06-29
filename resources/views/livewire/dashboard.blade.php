<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        @foreach ($stats as $stat)
            <flux:card>
                <div class="flex items-center gap-2">
                    <flux:icon name="{{ $stat['icon'] }}" class="size-6 text-neutral-500" />
                    <flux:text variant="strong">
                        {{ __($stat['name']) }}
                    </flux:text>
                </div>
                <div class="mt-4 text-3xl font-bold">
                    {{ $stat['value'] }}
                </div>
            </flux:card>
        @endforeach
    </div>
    <flux:card class="relative h-full flex-1 overflow-hidden">
        <div class="p-4">
            <flux:heading>
                Recent Form Submissions
            </flux:heading>
        </div>
        <flux:table :data="$recentSubmissions">
            <flux:table.columns>
                <flux:table.column>Form</flux:table.column>
                <flux:table.column>Submitted At</flux:table.column>
                <flux:table.column />
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($recentSubmissions as $submission)
                    <flux:table.row>
                        <flux:table.cell>{{ $submission->form->getTranslation('name', app()->getLocale()) }}</flux:table.cell>
                        <flux:table.cell>{{ $submission->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button variant="subtle" size="sm" href="{{ route('admin.forms.submissions', ['form' => $submission->form]) }}">
                                View
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <div class="flex flex-col items-center justify-center gap-2 p-6 text-center">
                                <flux:icon name="inbox-stack" class="size-12 text-neutral-400" />
                                <p class="font-semibold">{{ __('dashboard.no_submissions_yet') }}</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
