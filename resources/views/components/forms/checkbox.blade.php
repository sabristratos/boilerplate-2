@props([
    'label' => '',
    'value' => '',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'wireModel' => null,
    'description' => '',
    'error' => null,
    'options' => [],
])

@php
    $checkboxId = 'checkbox_' . uniqid();
    $hasError = $error !== null;
    $checkboxClasses = 'h-4 w-4 text-primary-600 focus:ring-primary-500 border-zinc-300 rounded transition-colors duration-200';
    
    if ($hasError) {
        $checkboxClasses .= ' border-red-500 focus:ring-red-500';
    }
    
    if ($disabled) {
        $checkboxClasses .= ' bg-zinc-100 dark:bg-zinc-800 cursor-not-allowed';
    }
@endphp

<div class="space-y-2" @click.stop>
    @if(isset($options) && is_array($options) && count($options) > 0)
        <!-- Multiple checkboxes -->
        @if($label)
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        
        <div class="space-y-3">
            @foreach($options as $option)
                @php
                    $optionId = 'checkbox_' . uniqid() . '_' . $loop->index;
                @endphp
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="{{ $optionId }}"
                            type="checkbox"
                            value="{{ $option['value'] ?? '' }}"
                            {{ $attributes->merge(['class' => $checkboxClasses]) }}
                            @if($disabled) disabled @endif
                            @if($wireModel) wire:model="{{ $wireModel }}" @endif
                        />
                    </div>
                    
                    <div class="ml-3 text-sm">
                        <label for="{{ $optionId }}" class="font-medium text-zinc-700 dark:text-zinc-300 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            {{ $option['label'] ?? $option['value'] ?? '' }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Single checkbox -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input
                    id="{{ $checkboxId }}"
                    type="checkbox"
                    value="{{ $value }}"
                    {{ $attributes->merge(['class' => $checkboxClasses]) }}
                    @if($checked) checked @endif
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    @if($wireModel) wire:model="{{ $wireModel }}" @endif
                />
            </div>
            
            <div class="ml-3 text-sm">
                <label for="{{ $checkboxId }}" class="font-medium text-zinc-700 dark:text-zinc-300 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                
                @if($description)
                    <p class="text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                @endif
            </div>
        </div>
    @endif
    
    @if($hasError)
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ $error }}
        </p>
    @endif
</div> 