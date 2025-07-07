@props(['selectedElement', 'selectedElementIndex', 'availableIcons'])
@if($selectedElement['type'] !== 'textarea')
    <div class="space-y-4">
        <flux:heading size="sm" class="flex items-center gap-2">
            <flux:icon name="photo" class="size-4" />
            Icon
        </flux:heading>
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.fluxProps.icon" 
            label="Leading Icon"
            placeholder="Choose an icon..."
            variant="listbox"
            searchable
        >
            <flux:select.option value="">
                <div class="flex items-center gap-2">
                    <flux:icon name="minus" variant="mini" class="text-zinc-400" />
                    No icon
                </div>
            </flux:select.option>
            @foreach($availableIcons as $iconKey => $iconName)
                <flux:select.option value="{{ $iconKey }}">
                    <div class="flex items-center gap-2">
                        <flux:icon name="{{ $iconKey }}" variant="mini" class="text-zinc-500" />
                        {{ $iconName }}
                    </div>
                </flux:select.option>
            @endforeach
        </flux:select>
    </div>
@endif 
