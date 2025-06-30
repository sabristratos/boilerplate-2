<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="lg">Form Submissions</flux:heading>
            <flux:text variant="subtle">{{ $form->getTranslation('name', 'en') }}</flux:text>
        </div>
        <flux:button href="{{ route('admin.forms.edit', $form) }}" icon="arrow-left">
            Back to Form
        </flux:button>
    </div>

    @if($submissions->count() > 0)
        <flux:card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left p-4 font-medium">Date</th>
                            <th class="text-left p-4 font-medium">IP Address</th>
                            <th class="text-left p-4 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="p-4">
                                    <flux:text size="sm">{{ $submission->created_at->format('M j, Y g:i A') }}</flux:text>
                                </td>
                                <td class="p-4">
                                    <flux:text size="sm">{{ $submission->ip_address }}</flux:text>
                                </td>
                                <td class="p-4">
                                    <flux:button 
                                        size="sm" 
                                        variant="ghost" 
                                        icon="eye"
                                        tooltip="View submission details"
                                    >
                                        View Data
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $submissions->links() }}
            </div>
        </flux:card>
    @else
        <flux:callout variant="secondary" icon="information-circle">
            <flux:callout.heading>No Submissions Yet</flux:callout.heading>
            <flux:callout.text>
                This form hasn't received any submissions yet. Once users start submitting the form, their responses will appear here.
            </flux:callout.text>
        </flux:callout>
    @endif
</div> 