@props(['selectedElement', 'selectedElementIndex'])
<div class="space-y-4">
    <flux:heading size="sm" class="flex items-center gap-2">
        <flux:icon name="calendar-days" class="size-4" />
        Date Picker Configuration
    </flux:heading>
    <!-- Basic Settings -->
    <div class="space-y-3">
        <flux:heading size="xs">Basic Settings</flux:heading>
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.mode" 
            label="Mode"
        >
            <flux:select.option value="single">Single Date</flux:select.option>
            <flux:select.option value="range">Date Range</flux:select.option>
        </flux:select>
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.months" 
            type="number"
            label="Months to Display" 
            placeholder="1"
            help="Number of months to show (1-12)"
        />
        <flux:select 
            wire:model.live="elements.{{ $selectedElementIndex }}.properties.size" 
            label="Calendar Size"
        >
            <flux:select.option value="sm">Small</flux:select.option>
            <flux:select.option value="default">Default</flux:select.option>
            <flux:select.option value="lg">Large</flux:select.option>
            <flux:select.option value="xl">Extra Large</flux:select.option>
            <flux:select.option value="2xl">2XL</flux:select.option>
        </flux:select>
    </div>
    <!-- Date Constraints -->
    <div class="space-y-3">
        <flux:heading size="xs">Date Constraints</flux:heading>
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.min" 
            label="Minimum Date" 
            placeholder="e.g. 2024-01-01 or today"
            help="Earliest selectable date"
        />
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.max" 
            label="Maximum Date" 
            placeholder="e.g. 2030-12-31 or today"
            help="Latest selectable date"
        />
        @if(($selectedElement['properties']['mode'] ?? null) === 'range')
            <flux:input 
                wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.minRange" 
                type="number"
                label="Minimum Range (days)" 
                placeholder="e.g. 3"
                help="Minimum number of days in range"
            />
            <flux:input 
                wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.maxRange" 
                type="number"
                label="Maximum Range (days)" 
                placeholder="e.g. 30"
                help="Maximum number of days in range"
            />
        @endif
    </div>
    <!-- Display Options -->
    <div class="space-y-3">
        <flux:heading size="xs">Display Options</flux:heading>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.weekNumbers" />
            <flux:label>Show Week Numbers</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.selectableHeader" />
            <flux:label>Selectable Header</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.withToday" />
            <flux:label>Today Shortcut</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.withInputs" />
            <flux:label>Show Date Inputs</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.withConfirmation" />
            <flux:label>Require Confirmation</flux:label>
        </flux:field>
    </div>
    <!-- Presets -->
    <div class="space-y-3">
        <flux:heading size="xs">Presets</flux:heading>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.withPresets" />
            <flux:label>Enable Presets</flux:label>
        </flux:field>
        @if($selectedElement['properties']['withPresets'] ?? false)
            <flux:textarea 
                wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.presets" 
                label="Available Presets" 
                placeholder="today yesterday thisWeek last7Days thisMonth yearToDate allTime"
                help="Space-separated list of preset options"
                rows="3"
            />
        @endif
    </div>
    <!-- Behavior -->
    <div class="space-y-3">
        <flux:heading size="xs">Behavior</flux:heading>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.clearable" />
            <flux:label>Clearable</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.disabled" />
            <flux:label>Disabled</flux:label>
        </flux:field>
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.invalid" />
            <flux:label>Invalid State</flux:label>
        </flux:field>
    </div>
    <!-- Additional Properties -->
    <div class="space-y-3">
        <flux:heading size="xs">Additional Properties</flux:heading>
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.description" 
            label="Description" 
            placeholder="Help text for users"
        />
        <flux:field variant="inline">
            <flux:switch wire:model.live="elements.{{ $selectedElementIndex }}.properties.descriptionTrailing" />
            <flux:label>Description Below</flux:label>
        </flux:field>
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.badge" 
            label="Badge" 
            placeholder="e.g. Required, New"
        />
        <flux:input 
            wire:model.live.debounce="elements.{{ $selectedElementIndex }}.properties.locale" 
            label="{{ __('labels.locale') }}" 
            placeholder="e.g. fr, en-US, ja-JP"
            help="Leave empty for browser default (will use 'en')"
            value="{{ $selectedElement['properties']['locale'] ?? app()->getLocale() ?? 'en' }}"
        />
    </div>
</div> 
