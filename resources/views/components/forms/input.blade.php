@props([
    'type' => 'text',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'clearable' => false,
    'copyable' => false,
    'viewable' => false,
    'icon' => null,
    'iconTrailing' => null,
    'badge' => '',
    'description' => '',
    'descriptionTrailing' => false,
    'error' => null,
    'wireModel' => null,
    'min' => null,
    'max' => null,
    'step' => null,
])

@php
    // Convert string boolean values to actual booleans
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $clearable = filter_var($clearable, FILTER_VALIDATE_BOOLEAN);
    $copyable = filter_var($copyable, FILTER_VALIDATE_BOOLEAN);
    $viewable = filter_var($viewable, FILTER_VALIDATE_BOOLEAN);
    $descriptionTrailing = filter_var($descriptionTrailing, FILTER_VALIDATE_BOOLEAN);
    
    $inputId = 'input_' . uniqid();
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
    
    // Handle password visibility toggle
    $showPassword = false;
    if ($type === 'password' && $viewable) {
        $showPassword = true;
    }
@endphp



<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
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
            id="{{ $inputId }}"
            type="{{ $showPassword ? 'text' : $type }}"
            {{ $attributes->merge(['class' => $inputClasses . ($icon ? ' pl-10' : '') . ($iconTrailing || $clearable || $copyable || $showPassword ? ' pr-10' : '')]) }}
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step !== null) step="{{ $step }}" @endif
            @if($wireModel) wire:model="{{ $wireModel }}" @endif
        />
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1">
            @if($showPassword)
                <button
                    type="button"
                    x-data="{ showPassword: false }"
                    @click="showPassword = !showPassword; $el.previousElementSibling.type = showPassword ? 'text' : 'password'"
                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                >
                    <x-flux::icon name="eye" x-show="!showPassword" class="h-4 w-4" />
                    <x-flux::icon name="eye-slash" x-show="showPassword" class="h-4 w-4" />
                </button>
            @endif
            
            @if($copyable)
                <button
                    type="button"
                    @click="navigator.clipboard.writeText($el.previousElementSibling.value)"
                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                    title="Copy to clipboard"
                >
                    <x-flux::icon name="clipboard" class="h-4 w-4" />
                </button>
            @endif
            
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