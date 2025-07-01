@props(['selectedElement', 'selectedElementIndex', 'activeBreakpoint'])
<div class="space-y-4">
    <flux:heading size="md" class="flex items-center gap-2">
        Styles ({{ Str::title($activeBreakpoint) }})
        <flux:tooltip toggleable>
            <flux:button icon="information-circle" size="sm" variant="ghost" />
            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                <p>Configure how this element appears on different screen sizes. The layout will automatically adjust based on the selected breakpoint.</p>
                <p><strong>Mobile:</strong> Phones and small tablets (up to 768px)</p>
                <p><strong>Tablet:</strong> Medium tablets (768px - 1024px)</p>
                <p><strong>Desktop:</strong> Large screens (1024px and above)</p>
            </flux:tooltip.content>
        </flux:tooltip>
    </flux:heading>
    <flux:callout variant="secondary" icon="information-circle">
        <flux:callout.text>
            Width settings for <strong>{{ Str::title($activeBreakpoint) }}</strong> breakpoint. 
            Elements will automatically flow in a 12-column grid system.
        </flux:callout.text>
    </flux:callout>
    <div class="flex gap-2 mb-4">
        <flux:button 
            wire:click="$set('activeBreakpoint', 'mobile')" 
            :variant="$activeBreakpoint === 'mobile' ? 'primary' : 'ghost'"
            size="sm"
            tooltip="Mobile breakpoint (up to 768px)"
        >
            Mobile
        </flux:button>
        <flux:button 
            wire:click="$set('activeBreakpoint', 'tablet')" 
            :variant="$activeBreakpoint === 'tablet' ? 'primary' : 'ghost'"
            size="sm"
            tooltip="Tablet breakpoint (768px - 1024px)"
        >
            Tablet
        </flux:button>
        <flux:button 
            wire:click="$set('activeBreakpoint', 'desktop')" 
            :variant="$activeBreakpoint === 'desktop' ? 'primary' : 'ghost'"
            size="sm"
            tooltip="Desktop breakpoint (1024px and above)"
        >
            Desktop
        </flux:button>
    </div>
    <flux:select 
        wire:change="updateElementWidth('{{ $selectedElement['id'] }}', '{{ $activeBreakpoint }}', $event.target.value)" 
        label="Width" 
        value="{{ $selectedElement['styles'][$activeBreakpoint]['width'] ?? 'full' }}"
        wire:key="width-select-{{ $selectedElement['id'] }}-{{ $activeBreakpoint }}"
        tooltip="Choose how much horizontal space this element takes up in the 12-column grid system"
    >
        <flux:select.option value="full">Full Width</flux:select.option>
        <flux:select.option value="1/2">Half Width (1/2)</flux:select.option>
        <flux:select.option value="1/3">One Third (1/3)</flux:select.option>
        <flux:select.option value="2/3">Two Thirds (2/3)</flux:select.option>
        <flux:select.option value="1/4">Quarter (1/4)</flux:select.option>
        <flux:select.option value="3/4">Three Quarters (3/4)</flux:select.option>
    </flux:select>
    @php
        $currentWidth = $selectedElement['styles'][$activeBreakpoint]['width'] ?? 'full';
        $widthDescription = match($currentWidth) {
            'full' => 'Takes up the full width (12 columns)',
            '1/2' => 'Takes up half the width (6 columns)',
            '1/3' => 'Takes up one-third width (4 columns)',
            '2/3' => 'Takes up two-thirds width (8 columns)',
            '1/4' => 'Takes up quarter width (3 columns)',
            '3/4' => 'Takes up three-quarters width (9 columns)',
            default => 'Takes up the full width (12 columns)'
        };
    @endphp
    <flux:text size="sm" variant="subtle">{{ $widthDescription }}</flux:text>
    <div class="mt-2">
        <flux:text size="xs" variant="subtle" class="mb-2">Grid Preview:</flux:text>
        <div class="grid grid-cols-12 gap-1 h-4">
            @php
                // Build grid cells HTML
                $gridCells = '';
                for ($i = 1; $i <= 12; $i++) {
                    $isActive = match($currentWidth) {
                        'full' => $i <= 12,
                        '1/2' => $i <= 6,
                        '1/3' => $i <= 4,
                        '2/3' => $i <= 8,
                        '1/4' => $i <= 3,
                        '3/4' => $i <= 9,
                        default => $i <= 12
                    };
                    $gridClass = $isActive ? 'bg-primary-500' : 'bg-zinc-200 dark:bg-zinc-700';
                    $gridCells .= '<div class="h-full rounded-sm ' . $gridClass . '"></div>';
                }
            @endphp
            {!! $gridCells !!}
        </div>
    </div>
    <flux:input 
        wire:model.live.debounce="elements.{{ $selectedElementIndex }}.styles.{{ $activeBreakpoint }}.fontSize" 
        label="Font Size" 
        placeholder="e.g. 16px or 1rem"
        tooltip="Set a custom font size for this element. Use CSS units like px, rem, em, or %"
    />
</div> 