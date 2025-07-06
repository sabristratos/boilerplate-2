<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">{{ __('revisions.title') }}</flux:heading>
                <flux:text class="text-gray-600">
                    {{ __('revisions.subtitle', ['model' => class_basename($model), 'count' => $this->revisions->total()]) }}
                </flux:text>
            </div>
            @if($selectedRevisionId)
                <div class="flex items-center space-x-3">
                    <flux:button 
                        variant="ghost" 
                        wire:click="startComparison"
                        :disabled="!$selectedRevisionId">
                        {{ __('revisions.compare') }}
                    </flux:button>
                    <flux:button 
                        variant="ghost" 
                        wire:click="$set('selectedRevisionId', null)">
                        {{ __('revisions.clear_selection') }}
                    </flux:button>
                </div>
            @endif
        </div>

        <!-- Comparison View -->
        @if($showComparison)
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
                <flux:heading size="md">{{ __('revisions.comparison.title') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <flux:label>{{ __('revisions.comparison.from') }}</flux:label>
                        <flux:select wire:model="compareRevisionId">
                            <flux:select.option value="">{{ __('revisions.comparison.select_revision') }}</flux:select.option>
                            @foreach($this->revisions as $revision)
                                <flux:select.option value="{{ $revision->id }}">
                                    {{ $revision->formatted_version }} - {{ $revision->created_at->format('M j, Y g:i A') }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="flex items-end">
                        <flux:button 
                            variant="ghost" 
                            wire:click="clearComparison">
                            {{ __('revisions.comparison.clear') }}
                        </flux:button>
                    </div>
                </div>
                @if($this->differences)
                    <div class="mt-6">
                        <flux:heading size="sm">{{ __('revisions.comparison.differences') }}</flux:heading>
                        <div class="mt-4 space-y-3">
                            @foreach($this->differences as $field => $diff)
                                <div class="border rounded-lg p-4">
                                    <flux:heading size="sm" class="mb-3">{{ getRevisionFieldLabel($field) }}</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <flux:text class="text-sm font-medium text-red-700 mb-1">{{ __('revisions.comparison.from') }}</flux:text>
                                            <div class="text-sm text-red-600">
                                                {!! formatRevisionValue($field, $diff['from'], class_basename($model)) !!}
                                            </div>
                                        </div>
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <flux:text class="text-sm font-medium text-green-700 mb-1">{{ __('revisions.comparison.to') }}</flux:text>
                                            <div class="text-sm text-green-600">
                                                {!! formatRevisionValue($field, $diff['to'], class_basename($model)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Revision List -->
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="space-y-4 p-4">
                @forelse($this->revisions as $revision)
                    <div class="py-4 @if(!$loop->first) border-t border-zinc-100 dark:border-zinc-800 @endif">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <flux:badge 
                                        :color="$revision->action === 'create' ? 'green' : ($revision->action === 'update' ? 'blue' : 'gray')">
                                        {{ $revision->action_description }}
                                    </flux:badge>
                                    <flux:text class="font-medium">{{ $revision->formatted_version }}</flux:text>
                                    @if($revision->is_published)
                                        <flux:badge color="green">{{ __('revisions.published') }}</flux:badge>
                                    @endif
                                </div>
                                @if($revision->description)
                                    <flux:text class="mt-1 text-gray-600">{{ $revision->description }}</flux:text>
                                @endif
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="user" class="w-4 h-4" />
                                        <flux:text>
                                            {{ $revision->user ? $revision->user->name : __('revisions.system_user') }}
                                        </flux:text>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <flux:icon name="clock" class="w-4 h-4" />
                                        <flux:text>{{ $revision->created_at->format('M j, Y g:i A') }}</flux:text>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <flux:button 
                                    size="sm" 
                                    variant="ghost"
                                    wire:click="selectRevision({{ $revision->id }})">
                                    {{ __('revisions.select') }}
                                </flux:button>
                                @if($revision->action !== 'create')
                                    <flux:button 
                                        size="sm" 
                                        variant="ghost"
                                        wire:click="showRevertConfirmation({{ $revision->id }})">
                                        {{ __('revisions.revert') }}
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                        @if($revision->changes && count($revision->changes) > 0)
                            <div class="mt-3 pt-3 border-t">
                                <flux:heading size="sm">{{ __('revisions.changes') }}</flux:heading>
                                <div class="mt-2 space-y-2">
                                    @foreach($revision->changes as $field => $value)
                                        <x-revision-field-display 
                                            :field="$field" 
                                            :value="$value" 
                                            :model-type="class_basename($model)" 
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <flux:icon name="document-text" class="w-12 h-12 text-gray-400 mx-auto" />
                        <flux:text class="mt-2 text-gray-500">{{ __('revisions.no_revisions') }}</flux:text>
                    </div>
                @endforelse
            </div>
            @if($this->revisions->hasPages())
                <div class="mt-6 px-4 pb-4">
                    {{ $this->revisions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Revert Confirmation Modal -->
    <flux:modal name="confirm-revert" :dismissible="false">
        <flux:heading size="lg">{{ __('revisions.confirm_revert.title') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('revisions.confirm_revert.message', ['version' => $revertingRevision?->formatted_version ?? '']) }}
        </flux:text>
        <x-slot name="actions">
            <flux:button variant="ghost" wire:click="closeConfirmationModal">
                {{ __('common.cancel') }}
            </flux:button>
            <flux:button variant="danger" wire:click="revertToRevision">
                {{ __('revisions.confirm_revert.confirm') }}
            </flux:button>
        </x-slot>
    </flux:modal>
</div> 