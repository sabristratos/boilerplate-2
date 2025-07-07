@props([
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'clearable' => false,
    'searchable' => false,
    'multiple' => false,
    'icon' => null,
    'iconTrailing' => null,
    'badge' => '',
    'description' => '',
    'descriptionTrailing' => false,
    'error' => null,
    'wireModel' => null,
    'variant' => 'default',
])

@php
    // Convert string boolean values to actual booleans
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $clearable = filter_var($clearable, FILTER_VALIDATE_BOOLEAN);
    $searchable = filter_var($searchable, FILTER_VALIDATE_BOOLEAN);
    $multiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $descriptionTrailing = filter_var($descriptionTrailing, FILTER_VALIDATE_BOOLEAN);
    
    $selectId = 'select_' . uniqid();
    $hasError = $error !== null;
    $selectClasses = 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200';
    
    if ($hasError) {
        $selectClasses .= ' border-red-500 focus:ring-red-500 focus:border-red-500';
    } else {
        $selectClasses .= ' border-zinc-300 dark:border-zinc-600';
    }
    
    if ($disabled) {
        $selectClasses .= ' bg-zinc-100 dark:bg-zinc-800 cursor-not-allowed';
    } else {
        $selectClasses .= ' bg-white dark:bg-zinc-900';
    }
@endphp



<div class="space-y-2">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
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
    
    <div class="relative" style="z-index: 1;" @click.stop>
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-flux::icon name="{{ $icon }}" class="h-5 w-5 text-zinc-400" />
            </div>
        @endif
        
        <select
            id="{{ $selectId }}"
            {{ $attributes->merge(['class' => $selectClasses . ($icon ? ' pl-10' : '') . ' pr-10']) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            @if($wireModel) wire:model="{{ $wireModel }}" @endif
            @click.stop
        >
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif
            
            @if(isset($options) && is_array($options))
                @foreach($options as $option)
                    <option value="{{ $option['value'] ?? '' }}">{{ $option['label'] ?? $option['value'] ?? '' }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1">
            @if($clearable)
                <button
                    type="button"
                    @click="$el.previousElementSibling.value = ''; $el.previousElementSibling.dispatchEvent(new Event('change'))"
                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                    title="Clear"
                >
                    <x-flux::icon name="x-mark" class="h-4 w-4" />
                </button>
            @endif
            
            @if($iconTrailing)
                <x-flux::icon name="{{ $iconTrailing }}" class="h-5 w-5 text-zinc-400" />
            @else
                <x-flux::icon name="chevron-down" class="h-5 w-5 text-zinc-400" />
            @endif
        </div>
    </div>
    
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