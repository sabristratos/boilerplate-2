@props(['selectedElement', 'selectedElementIndex', 'availableIcons'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="photo" class="size-4" />
        Icons
    </flux:heading>
    <div class="grid grid-cols-2 gap-3">
        @php
            // Build icon options HTML
            $leadingIconOptions = '<flux:select.option value="">No icon</flux:select.option>';
            $trailingIconOptions = '<flux:select.option value="">No icon</flux:select.option>';
            foreach ($availableIcons as $iconKey => $iconName) {
                $leadingIconOptions .= '<flux:select.option value="' . htmlspecialchars($iconKey) . '">' . htmlspecialchars($iconName) . '</flux:select.option>';
                $trailingIconOptions .= '<flux:select.option value="' . htmlspecialchars($iconKey) . '">' . htmlspecialchars($iconName) . '</flux:select.option>';
            }
        @endphp
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.icon" 
            label="Leading Icon"
            placeholder="Choose an icon..."
        >
            {!! $leadingIconOptions !!}
        </flux:select>
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.iconTrailing" 
            label="Trailing Icon"
            placeholder="Choose an icon..."
        >
            {!! $trailingIconOptions !!}
        </flux:select>
    </div>
</div> 