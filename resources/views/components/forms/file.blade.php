@props([
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'accept' => '',
    'maxSize' => '',
    'showPreview' => false,
    'icon' => null,
    'iconTrailing' => null,
    'badge' => '',
    'description' => '',
    'descriptionTrailing' => false,
    'error' => null,
    'wireModel' => null,
])

@php
    // Convert string boolean values to actual booleans
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $multiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $showPreview = filter_var($showPreview, FILTER_VALIDATE_BOOLEAN);
    $descriptionTrailing = filter_var($descriptionTrailing, FILTER_VALIDATE_BOOLEAN);
    
    $fileId = 'file_' . uniqid();
    $hasError = $error !== null;
    $fileClasses = 'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200';
    
    if ($hasError) {
        $fileClasses .= ' border-red-500 focus:ring-red-500 focus:border-red-500';
    } else {
        $fileClasses .= ' border-zinc-300 dark:border-zinc-600';
    }
    
    if ($disabled) {
        $fileClasses .= ' bg-zinc-100 dark:bg-zinc-800 cursor-not-allowed';
    } else {
        $fileClasses .= ' bg-white dark:bg-zinc-900';
    }
@endphp



<div class="space-y-2">
    @if($label)
        <label for="{{ $fileId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
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
            id="{{ $fileId }}"
            type="file"
            {{ $attributes->merge(['class' => $fileClasses . ($icon ? ' pl-10' : '') . ($iconTrailing ? ' pr-10' : '')]) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            @if($accept) accept="{{ $accept }}" @endif
            @if($wireModel) wire:model="{{ $wireModel }}" @endif
        />
        
        @if($iconTrailing)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <x-flux::icon name="{{ $iconTrailing }}" class="h-5 w-5 text-zinc-400" />
            </div>
        @endif
    </div>
    
    @if($description)
        <p class="text-sm {{ $descriptionTrailing ? 'text-right' : 'text-left' }} text-zinc-500 dark:text-zinc-400">
            {{ $description }}
        </p>
    @endif
    
    @if($maxSize)
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            Maximum file size: {{ $maxSize }}
        </p>
    @endif
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ $error }}
        </p>
    @endif
    
    @if($showPreview)
        <div x-data="{ files: [] }" x-show="files.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Selected Files:</h4>
            <div class="space-y-2">
                <template x-for="file in files" :key="file.name">
                    <div class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <x-flux::icon name="document" class="h-4 w-4 text-zinc-400" />
                            <span x-text="file.name" class="text-sm text-zinc-700 dark:text-zinc-300"></span>
                        </div>
                        <span x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'" class="text-xs text-zinc-500 dark:text-zinc-400"></span>
                    </div>
                </template>
            </div>
        </div>
    @endif
</div> 