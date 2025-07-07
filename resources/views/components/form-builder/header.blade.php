@props(['form', 'activeBreakpoint', 'isPreviewMode', 'hasUnsavedChanges'])

@php
    $latestRevision = $form->latestRevision();
@endphp

<div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50">
    <!-- Left Section: Navigation & Form Info -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.forms.index') }}" wire:navigate class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors">
            <flux:icon name="arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">{{ __('navigation.forms') }}</span>
        </a>
        <div class="w-px h-4 bg-zinc-300 dark:bg-zinc-600"></div>
        <div>
            <h1 class="text-sm font-medium text-zinc-900 dark:text-white">
                {{ $form->getTranslation('name', 'en') }}
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">ID: {{ $form->id }}</p>
        </div>
        <!-- Submissions Button -->
        <flux:tooltip content="{{ __('messages.forms.form_builder_interface.view_submissions_tooltip') }}">
            <flux:button 
                href="{{ route('admin.forms.submissions', $form) }}" 
                wire:navigate
                size="sm"
                variant="ghost"
                icon="document-text"
            >
                <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $form->submissions()->count() }})</span>
            </flux:button>
        </flux:tooltip>
    </div>
    
    <!-- Center Section: Breakpoint Controls -->
    <div class="flex items-center gap-2">
        <flux:button icon="computer-desktop" wire:click="$set('activeBreakpoint', 'desktop')" :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'" size="sm" />
        <flux:button icon="device-tablet" wire:click="$set('activeBreakpoint', 'tablet')" :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'" size="sm" />
        <flux:button icon="device-phone-mobile" wire:click="$set('activeBreakpoint', 'mobile')" :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'" size="sm" />
    </div>
    
    <!-- Right Section: Controls -->
    <div class="flex items-center gap-3">
        <!-- Last Updated Info -->
        @if($latestRevision)
            <flux:badge color="zinc" icon="clock" class="text-xs">
                {{ __('forms.builder.last_updated') }} {{ $latestRevision->created_at->diffForHumans() }}
            </flux:badge>
        @endif

        <!-- Preview Button -->
        <flux:tooltip content="{{ $isPreviewMode ? __('messages.forms.form_builder_interface.exit_preview_tooltip') : __('messages.forms.form_builder_interface.preview_tooltip') }}">
            <flux:button 
                variant="ghost" 
                icon="eye"
                wire:click="togglePreview"
                :variant="$isPreviewMode ? 'primary' : 'ghost'"
                size="sm"
            />
        </flux:tooltip>

        <!-- Actions Dropdown -->
        <flux:dropdown>
            <flux:button icon:trailing="chevron-down" size="sm" variant="ghost">
                {{ __('messages.forms.form_builder_interface.actions') }}
            </flux:button>
            <flux:menu>
                <!-- Save -->
                <flux:menu.item icon="check" wire:click="save">
                    {{ __('messages.forms.form_builder_interface.save') }}
                </flux:menu.item>
                
                <!-- Revision History -->
                <flux:menu.item icon="clock" href="{{ route('admin.revisions.show', ['modelType' => 'form', 'modelId' => $form->id]) }}">
                    {{ __('revisions.view_history') }}
                </flux:menu.item>
                
                <flux:menu.separator />
                
                <!-- Delete Form -->
                <flux:menu.item variant="danger" icon="trash" wire:click="confirmDeleteForm">
                    {{ __('messages.forms.form_builder_interface.delete_form') }}
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div> 
