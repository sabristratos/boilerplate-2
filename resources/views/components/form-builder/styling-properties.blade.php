@props(['selectedElement', 'selectedElementIndex', 'activeBreakpoint'])
<div class="space-y-4">
    <flux:heading size="lg" class="flex items-center gap-2">
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
    
    <!-- Desktop Width Select -->
    @if($activeBreakpoint === 'desktop')
        <flux:select 
            wire:change="updateElementWidth('{{ $selectedElement['id'] }}', 'desktop', $event.target.value)" 
            label="Width (Desktop)" 
            value="{{ $selectedElement['styles']['desktop']['width'] ?? 'full' }}"
            wire:key="width-select-{{ $selectedElement['id'] }}-desktop"
            tooltip="Choose how much horizontal space this element takes up in the 12-column grid system on desktop"
        >
            <flux:select.option value="full">Full Width</flux:select.option>
            <flux:select.option value="1/2">Half Width (1/2)</flux:select.option>
            <flux:select.option value="1/3">One Third (1/3)</flux:select.option>
            <flux:select.option value="2/3">Two Thirds (2/3)</flux:select.option>
            <flux:select.option value="1/4">Quarter (1/4)</flux:select.option>
            <flux:select.option value="3/4">Three Quarters (3/4)</flux:select.option>
        </flux:select>
    @endif

    <!-- Tablet Width Select -->
    @if($activeBreakpoint === 'tablet')
        <flux:select 
            wire:change="updateElementWidth('{{ $selectedElement['id'] }}', 'tablet', $event.target.value)" 
            label="Width (Tablet)" 
            value="{{ $selectedElement['styles']['tablet']['width'] ?? 'full' }}"
            wire:key="width-select-{{ $selectedElement['id'] }}-tablet"
            tooltip="Choose how much horizontal space this element takes up in the 12-column grid system on tablet"
        >
            <flux:select.option value="full">Full Width</flux:select.option>
            <flux:select.option value="1/2">Half Width (1/2)</flux:select.option>
            <flux:select.option value="1/3">One Third (1/3)</flux:select.option>
            <flux:select.option value="2/3">Two Thirds (2/3)</flux:select.option>
            <flux:select.option value="1/4">Quarter (1/4)</flux:select.option>
            <flux:select.option value="3/4">Three Quarters (3/4)</flux:select.option>
        </flux:select>
    @endif

    <!-- Mobile Width Select -->
    @if($activeBreakpoint === 'mobile')
        <flux:select 
            wire:change="updateElementWidth('{{ $selectedElement['id'] }}', 'mobile', $event.target.value)" 
            label="Width (Mobile)" 
            value="{{ $selectedElement['styles']['mobile']['width'] ?? 'full' }}"
            wire:key="width-select-{{ $selectedElement['id'] }}-mobile"
            tooltip="Choose how much horizontal space this element takes up in the 12-column grid system on mobile"
        >
            <flux:select.option value="full">Full Width</flux:select.option>
            <flux:select.option value="1/2">Half Width (1/2)</flux:select.option>
            <flux:select.option value="1/3">One Third (1/3)</flux:select.option>
            <flux:select.option value="2/3">Two Thirds (2/3)</flux:select.option>
            <flux:select.option value="1/4">Quarter (1/4)</flux:select.option>
            <flux:select.option value="3/4">Three Quarters (3/4)</flux:select.option>
        </flux:select>
    @endif
    
    @if($selectedElement['type'] === \App\Enums\FormElementType::SubmitButton->value)
        <!-- Desktop Alignment Select -->
        @if($activeBreakpoint === 'desktop')
            <flux:select 
                wire:change="updateElementAlignment('{{ $selectedElement['id'] }}', 'desktop', $event.target.value)" 
                label="{{ __('forms.field_types.submit_button.alignment_label') }} (Desktop)" 
                value="{{ $selectedElement['styles']['desktop']['alignment'] ?? 'center' }}"
                wire:key="alignment-select-{{ $selectedElement['id'] }}-desktop"
                tooltip="Choose how the submit button is aligned within its container on desktop"
            >
                <flux:select.option value="left">{{ __('forms.field_types.submit_button.align_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('forms.field_types.submit_button.align_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('forms.field_types.submit_button.align_right') }}</flux:select.option>
                <flux:select.option value="full">{{ __('forms.field_types.submit_button.align_full') }}</flux:select.option>
            </flux:select>
        @endif

        <!-- Tablet Alignment Select -->
        @if($activeBreakpoint === 'tablet')
            <flux:select 
                wire:change="updateElementAlignment('{{ $selectedElement['id'] }}', 'tablet', $event.target.value)" 
                label="{{ __('forms.field_types.submit_button.alignment_label') }} (Tablet)" 
                value="{{ $selectedElement['styles']['tablet']['alignment'] ?? 'center' }}"
                wire:key="alignment-select-{{ $selectedElement['id'] }}-tablet"
                tooltip="Choose how the submit button is aligned within its container on tablet"
            >
                <flux:select.option value="left">{{ __('forms.field_types.submit_button.align_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('forms.field_types.submit_button.align_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('forms.field_types.submit_button.align_right') }}</flux:select.option>
                <flux:select.option value="full">{{ __('forms.field_types.submit_button.align_full') }}</flux:select.option>
            </flux:select>
        @endif

        <!-- Mobile Alignment Select -->
        @if($activeBreakpoint === 'mobile')
            <flux:select 
                wire:change="updateElementAlignment('{{ $selectedElement['id'] }}', 'mobile', $event.target.value)" 
                label="{{ __('forms.field_types.submit_button.alignment_label') }} (Mobile)" 
                value="{{ $selectedElement['styles']['mobile']['alignment'] ?? 'center' }}"
                wire:key="alignment-select-{{ $selectedElement['id'] }}-mobile"
                tooltip="Choose how the submit button is aligned within its container on mobile"
            >
                <flux:select.option value="left">{{ __('forms.field_types.submit_button.align_left') }}</flux:select.option>
                <flux:select.option value="center">{{ __('forms.field_types.submit_button.align_center') }}</flux:select.option>
                <flux:select.option value="right">{{ __('forms.field_types.submit_button.align_right') }}</flux:select.option>
                <flux:select.option value="full">{{ __('forms.field_types.submit_button.align_full') }}</flux:select.option>
            </flux:select>
        @endif
    @endif
</div> 
