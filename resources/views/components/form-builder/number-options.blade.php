@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="hashtag" class="size-4" />
        Number Input Options
    </flux:heading>
    <div class="space-y-3">
        <flux:field variant="inline">
            <flux:switch wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.clearable" />
            <flux:label>Clearable</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.copyable" />
            <flux:label>Copyable</flux:label>
        </flux:field>
        <flux:input 
            wire:model.live.debounce="draftElements.{{ $selectedElementIndex }}.properties.min" 
            type="number"
            label="Minimum Value" 
            placeholder="e.g. 0"
            help="Leave empty for no minimum value"
        />
        <flux:input 
            wire:model.live.debounce="draftElements.{{ $selectedElementIndex }}.properties.max" 
            type="number"
            label="Maximum Value" 
            placeholder="e.g. 100"
            help="Leave empty for no maximum value"
        />
        <flux:input 
            wire:model.live.debounce="draftElements.{{ $selectedElementIndex }}.properties.step" 
            type="number"
            label="Step Value" 
            placeholder="e.g. 1, 0.1, 10"
            help="Increment/decrement step for the number input"
        />
    </div>
</div> 
