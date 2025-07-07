@php
    // Get the active breakpoint from the data passed by the renderer
    $activeBreakpoint = $activeBreakpoint ?? 'desktop';
    
    // Get alignment for the current breakpoint
    $alignment = $element->styles[$activeBreakpoint]['alignment'] ?? 'center';
    
    $alignmentClasses = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
        'full' => 'w-full',
    ];
    $buttonText = $element->properties['buttonText'] ?? __('forms.ui.submit_form');
@endphp

<div class="flex {{ $alignmentClasses[$alignment] ?? 'justify-center' }}">
    <x-frontend.button type="submit" size="lg" :class="$alignment === 'full' ? 'w-full' : ''">
        {{ $buttonText }}
    </x-frontend.button>
</div> 