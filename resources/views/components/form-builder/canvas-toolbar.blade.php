@props(['activeBreakpoint', 'isPreviewMode'])
<div class="flex justify-center items-center p-2 bg-white dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700/50 space-x-1">
    <flux:button icon="computer-desktop" wire:click="$set('activeBreakpoint', 'desktop')" :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'" />
    <flux:button icon="device-tablet" wire:click="$set('activeBreakpoint', 'tablet')" :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'" />
    <flux:button icon="device-phone-mobile" wire:click="$set('activeBreakpoint', 'mobile')" :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'" />

    <flux:spacer />

    <div class="flex items-center gap-2">
        <flux:button 
            wire:click="save" 
            icon="check"
            tooltip="Save your form changes"
        >
            Save
        </flux:button>
        <flux:button 
            variant="ghost" 
            icon="eye"
            wire:click="togglePreview"
            :variant="$isPreviewMode ? 'primary' : 'ghost'"
            tooltip="{{ $isPreviewMode ? 'Exit Preview Mode' : 'Preview the form as users will see it' }}"
        >
            {{ $isPreviewMode ? 'Exit Preview' : 'Preview' }}
        </flux:button>
    </div>
</div> 