@props(['field', 'value', 'modelType' => null])

<div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-200">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <flux:text class="text-sm font-medium text-gray-700 mb-1">
                {{ getRevisionFieldLabel($field) }}
            </flux:text>
            <div class="text-sm text-gray-600">
                {!! formatRevisionValue($field, $value, $modelType) !!}
            </div>
        </div>
        @if(is_array($value) && count($value) > 0 && !in_array($field, ['name', 'draft_name', 'title', 'draft_title', 'slug', 'draft_slug']))
            <flux:button 
                size="xs" 
                variant="ghost"
                @click="$dispatch('show-field-details', { field: '{{ $field }}', value: {{ json_encode($value) }} })"
                class="ml-2"
            >
                <flux:icon name="eye" class="w-3 h-3" />
            </flux:button>
        @endif
    </div>
</div> 