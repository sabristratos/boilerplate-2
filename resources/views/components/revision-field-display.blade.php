@props(['field', 'value', 'modelType' => null])

@php
    // Handle JSON strings that might be stored as strings
    $displayValue = $value;
    if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $displayValue = $decoded;
        }
    }
    
    // Generate a user-friendly summary for complex data
    $summary = '';
    $hasDetails = false;
    
    if (is_array($displayValue)) {
        if ($field === 'elements') {
            $elementTypes = collect($displayValue)->pluck('type')->filter()->unique();
            $summary = $elementTypes->count() . ' element(s): ' . $elementTypes->implode(', ');
            $hasDetails = count($displayValue) > 0;
        } elseif ($field === 'settings') {
            $settingKeys = array_keys($displayValue);
            $summary = count($settingKeys) . ' setting(s): ' . implode(', ', $settingKeys);
            $hasDetails = count($displayValue) > 0;
        } elseif (array_keys($displayValue) !== range(0, count($displayValue) - 1)) {
            // Associative array (like translations)
            $nonEmpty = array_filter($displayValue, fn($v) => !empty($v));
            $summary = count($nonEmpty) . ' translation(s): ' . implode(', ', array_keys($nonEmpty));
            $hasDetails = count($nonEmpty) > 0;
        } else {
            // Regular array
            $summary = count($displayValue) . ' item(s)';
            $hasDetails = count($displayValue) > 0;
        }
    } elseif (is_string($displayValue) && strlen($displayValue) > 50) {
        $summary = substr($displayValue, 0, 50) . '...';
        $hasDetails = true;
    } else {
        $summary = (string) $displayValue;
    }
@endphp

<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border-l-4 border-blue-200 dark:border-blue-600">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <flux:text class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ getRevisionFieldLabel($field) }}
            </flux:text>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($hasDetails)
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ $summary }}</span>
                        <flux:button 
                            size="xs" 
                            variant="ghost"
                            @click="$dispatch('show-field-details', { field: '{{ $field }}', value: {{ json_encode($displayValue) }} })"
                            icon="eye"
                            :tooltip="'View details'"
                        />
                    </div>
                @else
                    <span class="font-medium">{{ $summary }}</span>
                @endif
            </div>
        </div>
    </div>
</div> 