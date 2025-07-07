@props([
    'label' => '',
    'required' => false,
    'disabled' => false,
    'wireModel' => null,
    'description' => '',
    'error' => null,
    'variant' => 'default',
])

@php
    // Convert string boolean values to actual booleans
    $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $descriptionTrailing = filter_var($descriptionTrailing ?? false, FILTER_VALIDATE_BOOLEAN);
    
    $groupId = 'checkbox-group_' . uniqid();
    $hasError = $error !== null;
@endphp



<div class="space-y-3">
    @if($label)
        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            
            @if($description)
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $description }}</p>
            @endif
        </div>
    @endif
    
    <div id="{{ $groupId }}" class="{{ $groupClasses }}">
        {{ $slot }}
    </div>
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ $error }}
        </p>
    @endif
</div> 