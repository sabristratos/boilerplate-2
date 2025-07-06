@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="sparkles" class="size-4" />
        Input Actions
    </flux:heading>
    <div class="space-y-3">
        @if($selectedElement['type'] !== 'textarea')
            <flux:field variant="inline">
                <flux:switch wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.fluxProps.clearable" />
                <flux:label>Clearable</flux:label>
            </flux:field>
            <flux:field variant="inline">
                <flux:switch wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.fluxProps.copyable" />
                <flux:label>Copyable</flux:label>
            </flux:field>
        @endif
        @if($selectedElement['type'] === 'password')
            <flux:field variant="inline">
                <flux:switch wire:model.live="draftElements.{{ $selectedElementIndex }}.properties.viewable" />
                <flux:label>Show/Hide Toggle</flux:label>
            </flux:field>
        @endif
    </div>
</div> 
