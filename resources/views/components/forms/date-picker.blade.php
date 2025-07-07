@props([
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'clearable' => false,
    'mode' => 'single',
    'min' => null,
    'max' => null,
    'withPresets' => false,
    'presets' => '',
    'weekNumbers' => false,
    'selectableHeader' => false,
    'withToday' => false,
    'withInputs' => false,
    'withConfirmation' => false,
    'icon' => null,
    'iconTrailing' => null,
    'badge' => '',
    'description' => '',
    'descriptionTrailing' => false,
    'error' => null,
    'wireModel' => null,
])

@php
    $dateId = 'date_' . uniqid();
    $hasError = $error !== null;
    $inputClasses = 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200';
    
    if ($hasError) {
        $inputClasses .= ' border-red-500 focus:ring-red-500 focus:border-red-500';
    } else {
        $inputClasses .= ' border-zinc-300 dark:border-zinc-600';
    }
    
    if ($disabled) {
        $inputClasses .= ' bg-zinc-100 dark:bg-zinc-800 cursor-not-allowed';
    } else {
        $inputClasses .= ' bg-white dark:bg-zinc-900';
    }
    
    // Parse presets
    $presetArray = [];
    if ($withPresets && $presets) {
        $presetArray = array_filter(explode(' ', $presets));
    }
@endphp

<div class="space-y-2" @click.stop>
    @if($label)
        <label for="{{ $dateId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
            @if($badge)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                    {{ $badge }}
                </span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-flux::icon name="{{ $icon }}" class="h-5 w-5 text-zinc-400" />
            </div>
        @endif
        
        <input
            id="{{ $dateId }}"
            type="date"
            {{ $attributes->merge(['class' => $inputClasses . ($icon ? ' pl-10' : '') . ($iconTrailing || $clearable ? ' pr-10' : '')]) }}
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($min) min="{{ $min }}" @endif
            @if($max) max="{{ $max }}" @endif
            @if($wireModel) wire:model="{{ $wireModel }}" @endif
            @click.stop
        />
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1">
            @if($clearable)
                <button
                    type="button"
                    @click="$el.previousElementSibling.value = ''; $el.previousElementSibling.dispatchEvent(new Event('input'))"
                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                    title="Clear"
                >
                    <x-flux::icon name="x-mark" class="h-4 w-4" />
                </button>
            @endif
            
            @if($iconTrailing)
                <x-flux::icon name="{{ $iconTrailing }}" class="h-5 w-5 text-zinc-400" />
            @else
                <x-flux::icon name="calendar" class="h-5 w-5 text-zinc-400" />
            @endif
        </div>
    </div>
    
    @if($withPresets && !empty($presetArray))
        <div class="flex flex-wrap gap-2 mt-2">
            @foreach($presetArray as $preset)
                <button
                    type="button"
                    @click="
                        const input = document.getElementById('{{ $dateId }}');
                        const today = new Date();
                        let targetDate = new Date();
                        
                        switch('{{ $preset }}') {
                            case 'today':
                                targetDate = today;
                                break;
                            case 'yesterday':
                                targetDate.setDate(today.getDate() - 1);
                                break;
                            case 'thisWeek':
                                targetDate.setDate(today.getDate() - today.getDay());
                                break;
                            case 'last7Days':
                                targetDate.setDate(today.getDate() - 7);
                                break;
                            case 'thisMonth':
                                targetDate.setDate(1);
                                break;
                            case 'yearToDate':
                                targetDate = new Date(today.getFullYear(), 0, 1);
                                break;
                            case 'allTime':
                                targetDate = new Date(2000, 0, 1);
                                break;
                        }
                        
                        input.value = targetDate.toISOString().split('T')[0];
                        input.dispatchEvent(new Event('input'));
                    "
                    class="px-2 py-1 text-xs bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded transition-colors"
                >
                    {{ ucfirst($preset) }}
                </button>
            @endforeach
        </div>
    @endif
    
    @if($description)
        <p class="text-sm {{ $descriptionTrailing ? 'text-right' : 'text-left' }} text-zinc-500 dark:text-zinc-400">
            {{ $description }}
        </p>
    @endif
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ $error }}
        </p>
    @endif
</div> 