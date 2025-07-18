<div>
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">{{ __('forms.submission_details') }}</flux:heading>
        
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
            <flux:breadcrumbs.item href="{{ route('admin.forms.index') }}">{{ __('navigation.forms') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('admin.forms.edit', $form) }}">{{ $form->getTranslation('name', app()->getLocale()) }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('admin.forms.submissions', $form) }}">{{ __('labels.submissions') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('forms.submission_details') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Submission Data -->
        <div class="lg:col-span-2">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('forms.submission_data') }}</flux:heading>
                
                @if(is_array($formattedData) && count($formattedData) > 0)
                    <div class="space-y-4">
                        @foreach($formattedData as $key => $value)
                            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4 last:border-b-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <flux:text variant="strong" class="block mb-1">{{ $key }}</flux:text>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            @if(is_string($value))
                                                {{ $value }}
                                            @elseif(is_array($value))
                                                <div class="space-y-1">
                                                    @foreach($value as $subKey => $subValue)
                                                        <div>
                                                            <span class="font-medium">{{ $subKey }}:</span> 
                                                            <span>{{ is_string($subValue) ? $subValue : json_encode($subValue) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ json_encode($value) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:callout variant="secondary" icon="information-circle">
                        <flux:callout.heading>{{ __('forms.no_submission_data') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('forms.no_submission_data_description') }}</flux:callout.text>
                    </flux:callout>
                @endif

                @if($hasSensitiveData)
                    <div class="mt-4">
                        <flux:callout variant="warning" icon="exclamation-triangle">
                            <flux:callout.heading>{{ __('forms.sensitive_data_warning') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('forms.sensitive_data_description') }}</flux:callout.text>
                        </flux:callout>
                    </div>
                @endif
            </flux:card>
        </div>

        <!-- Submission Metadata -->
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('forms.submission_metadata') }}</flux:heading>
                
                <div class="space-y-4">
                    <div>
                        <flux:text variant="strong" class="block mb-1">{{ __('forms.submitted_at') }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            {{ $submissionDto->createdAt?->format('F j, Y \a\t g:i A') }}
                        </flux:text>
                        @if($submissionAge)
                            <flux:text size="xs" class="text-zinc-500">{{ $submissionAge }}</flux:text>
                        @endif
                    </div>

                    <div>
                        <flux:text variant="strong" class="block mb-1">{{ __('forms.ip_address') }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            {{ $submissionDto->ipAddress }}
                        </flux:text>
                    </div>

                    @if($submissionDto->userAgent)
                        <div>
                            <flux:text variant="strong" class="block mb-1">{{ __('forms.user_agent') }}</flux:text>
                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 break-all">
                                {{ $submissionDto->userAgent }}
                            </flux:text>
                        </div>
                    @endif

                    <div>
                        <flux:text variant="strong" class="block mb-1">{{ __('forms.submission_id') }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 font-mono">
                            {{ $submissionDto->id }}
                        </flux:text>
                    </div>

                    <div>
                        <flux:text variant="strong" class="block mb-1">{{ __('forms.form_name') }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                            {{ $formDto->getNameForLocale() }}
                        </flux:text>
                    </div>
                </div>
            </flux:card>

            <!-- Actions -->
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('labels.actions') }}</flux:heading>
                
                <div class="space-y-2">
                    <flux:button
                        href="{{ route('admin.forms.submissions', $form) }}"
                        variant="ghost"
                        icon="arrow-left"
                        class="w-full justify-start"
                        wire:navigate
                    >
                        {{ __('buttons.back_to_submissions') }}
                    </flux:button>

                    <flux:button
                        href="{{ route('admin.forms.edit', $form) }}"
                        variant="ghost"
                        icon="pencil-square"
                        class="w-full justify-start"
                        wire:navigate
                    >
                        {{ __('buttons.edit_form') }}
                    </flux:button>

                    <flux:button
                        wire:click="deleteSubmission"
                        variant="ghost"
                        icon="trash"
                        class="w-full justify-start text-red-600 hover:text-red-700"
                    >
                        {{ __('buttons.delete_submission') }}
                    </flux:button>
                </div>
            </flux:card>
        </div>
    </div>
</div> 