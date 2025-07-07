@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="document-arrow-up" class="size-4" />
        File Upload Options
    </flux:heading>
    <div class="space-y-3">
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.multiple" />
            <flux:label>Multiple Files</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.showPreview" />
            <flux:label>Show File Preview</flux:label>
        </flux:field>
        <flux:input 
            wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.accept" 
            label="Accepted File Types" 
            placeholder="e.g. .pdf,.doc,.docx or image/*"
            help="Comma-separated list of file extensions or MIME types"
        />
        <flux:input 
            wire:model.live.debounce.500ms="elements.{{ $selectedElementIndex }}.properties.maxSize" 
            label="Maximum File Size" 
            placeholder="e.g. 5MB, 10MB"
            help="Maximum allowed file size"
        />
    </div>
</div> 
