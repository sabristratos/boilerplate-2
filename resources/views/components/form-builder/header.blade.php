@props(['form', 'activeBreakpoint', 'isPreviewMode', 'hasUnsavedChanges'])

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
    </div>
    
    <!-- Center Section: Breakpoint Controls -->
    <div class="flex items-center gap-2">
        <flux:button icon="computer-desktop" wire:click="$set('activeBreakpoint', 'desktop')" :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'" size="sm" />
        <flux:button icon="device-tablet" wire:click="$set('activeBreakpoint', 'tablet')" :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'" size="sm" />
        <flux:button icon="device-phone-mobile" wire:click="$set('activeBreakpoint', 'mobile')" :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'" size="sm" />
    </div>
    
    <!-- Right Section: Controls -->
    <div class="flex items-center gap-4">
        <!-- Submissions Button -->
        <flux:button 
            href="{{ route('admin.forms.submissions', $form) }}" 
            wire:navigate
            size="sm"
            variant="ghost"
            icon="document-text"
            :tooltip="__('messages.forms.form_builder_interface.view_submissions_tooltip')"
        >
            {{ __('messages.forms.form_builder_interface.submissions') }} ({{ $form->submissions()->count() }})
        </flux:button>

        <!-- Unsaved Changes Badge -->
        @if($hasUnsavedChanges)
            <flux:badge color="amber" icon="exclamation-triangle" class="text-xs">
                {{ __('messages.forms.form_builder_interface.unsaved_changes') }}
            </flux:badge>
        @endif

        <!-- Save Button -->
        <flux:button 
            wire:click="save" 
            icon="check"
            variant="primary"
            size="sm"
            :tooltip="__('messages.forms.form_builder_interface.save_tooltip')"
        >
            {{ __('messages.forms.form_builder_interface.save') }}
        </flux:button>

        <!-- Preview Button -->
        <flux:button 
            variant="ghost" 
            icon="eye"
            wire:click="togglePreview"
            :variant="$isPreviewMode ? 'primary' : 'ghost'"
            size="sm"
            :tooltip="$isPreviewMode ? __('messages.forms.form_builder_interface.exit_preview_tooltip') : __('messages.forms.form_builder_interface.preview_tooltip')"
        >
            {{ $isPreviewMode ? __('messages.forms.form_builder_interface.exit_preview') : __('messages.forms.form_builder_interface.preview') }}
        </flux:button>
    </div>
</div> 
