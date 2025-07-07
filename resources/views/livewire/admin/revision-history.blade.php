<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('revisions.title') }}</flux:heading>
                <flux:text class="text-gray-600 mt-1">
                    {{ __('revisions.subtitle', ['model' => class_basename($model), 'count' => $this->revisions->total()]) }}
                </flux:text>
            </div>
            
            @if($selectedRevisionId)
                <div class="flex items-center gap-3">
                    <flux:button 
                        variant="primary" 
                        wire:click="startComparison"
                        :disabled="!$selectedRevisionId"
                        icon="arrows-right-left"
                    >
                        {{ __('revisions.compare') }}
                    </flux:button>
                    <flux:button 
                        variant="ghost" 
                        wire:click="$set('selectedRevisionId', null)"
                        icon="x-mark"
                    >
                        {{ __('revisions.clear_selection') }}
                    </flux:button>
                </div>
            @endif
        </div>

        <!-- Comparison View -->
        @if($showComparison)
            <flux:card class="space-y-4">
                <flux:heading size="lg">{{ __('revisions.comparison.title') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            wire:click="clearComparison"
                            icon="x-mark"
                        >
                            {{ __('revisions.comparison.clear') }}
                        </flux:button>
                    </div>
                </div>
                
                @if($this->differences)
                    <div class="mt-6">
                        <flux:heading size="md">{{ __('revisions.comparison.differences') }}</flux:heading>
                        <div class="mt-4 space-y-4">
                            @foreach($this->differences as $field => $diff)
                                <flux:card size="sm" class="p-4">
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
                                </flux:card>
                            @endforeach
                        </div>
                    </div>
                @endif
            </flux:card>
        @endif

        <!-- Revision List -->
        <div class="space-y-4">
            @forelse($this->revisions as $revision)
                <flux:card class="p-6 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 space-y-3">
                            <!-- Revision Header -->
                            <div class="flex items-center gap-3">
                                <flux:badge 
                                    :color="$revision->action === 'create' ? 'green' : ($revision->action === 'update' ? 'blue' : 'gray')"
                                    size="sm"
                                >
                                    {{ $revision->action_description }}
                                </flux:badge>
                                
                                <flux:heading size="md">{{ $revision->formatted_version }}</flux:heading>
                                
                                @if($revision->is_published)
                                    <flux:badge color="green" size="sm" icon="check-circle">
                                        {{ __('revisions.published') }}
                                    </flux:badge>
                                @endif
                            </div>
                            
                            <!-- Revision Description -->
                            @if($revision->description)
                                <flux:text class="text-gray-600">{{ $revision->description }}</flux:text>
                            @endif
                            
                            <!-- Revision Metadata -->
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <div class="flex items-center gap-1">
                                    <flux:icon name="user" class="w-4 h-4" />
                                    <flux:text>
                                        {{ $revision->user ? $revision->user->name : __('revisions.system_user') }}
                                    </flux:text>
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:icon name="clock" class="w-4 h-4" />
                                    <flux:text>{{ $revision->created_at->format('M j, Y g:i A') }}</flux:text>
                                </div>
                            </div>
                            
                            <!-- Changes Summary -->
                            @if($revision->changes && count($revision->changes) > 0)
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <flux:heading size="sm" class="mb-2">{{ __('revisions.changes') }}</flux:heading>
                                    <div class="space-y-2">
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
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 ml-4">
                            @if($revision->action !== 'create')
                                <flux:button 
                                    size="sm" 
                                    variant="ghost"
                                    wire:click="showRevertConfirmation({{ $revision->id }})"
                                    icon="arrow-path"
                                    :tooltip="__('revisions.revert_tooltip')"
                                >
                                    {{ __('revisions.revert') }}
                                </flux:button>
                            @endif
                            
                            <flux:button 
                                size="sm" 
                                variant="ghost"
                                wire:click="selectRevision({{ $revision->id }})"
                                icon="eye"
                                :tooltip="__('revisions.select_tooltip')"
                            >
                                {{ __('revisions.select') }}
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <flux:card class="text-center py-12">
                    <flux:icon name="document-text" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <flux:heading size="lg" class="mb-2">{{ __('revisions.no_revisions_title') }}</flux:heading>
                    <flux:text class="text-gray-500">{{ __('revisions.no_revisions') }}</flux:text>
                </flux:card>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($this->revisions->hasPages())
            <div class="mt-6">
                {{ $this->revisions->links() }}
            </div>
        @endif
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

    <!-- Field Details Modal -->
    <flux:modal wire:model.live="showFieldDetails" class="max-w-4xl">
        <flux:heading size="lg">Field Details</flux:heading>
        <div class="mt-4">
            <flux:text class="text-sm text-gray-600 mb-3">{{ $fieldDetailsLabel ?? '' }}</flux:text>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 max-h-96 overflow-y-auto">
                <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $fieldDetailsValue ?? '' }}</pre>
            </div>
        </div>
        <x-slot name="actions">
            <flux:button variant="ghost" wire:click="$set('showFieldDetails', false)">
                {{ __('common.close') }}
            </flux:button>
        </x-slot>
    </flux:modal>
</div> 