<div class="space-y-4">
    <label class="flux-label">{{ $label }}</label>
    @if($description)
        <p class="text-sm text-gray-500">{{ $description }}</p>
    @endif
    
    <div class="space-y-2">
        @foreach($items as $index => $item)
            <div wire:key="{{ $wireModel }}.{{ $index }}" class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-lg">
                <div class="grid grid-cols-2 gap-2 flex-1">
                    <flux:input wire:model.live="{{ $wireModel }}.{{ $index }}.label" placeholder="{{ __('forms.label') }}" />
                    <flux:input wire:model.live="{{ $wireModel }}.{{ $index }}.value" placeholder="{{ __('forms.value') }}" />
                </div>
                <flux:button wire:click="removeRepeaterItem('{{ $wireModelKey }}', {{ $index }})" icon="trash" variant="ghost" size="sm" />
            </div>
        @endforeach
    </div>

    <flux:button wire:click="addRepeaterItem('{{ $wireModelKey }}')" icon="plus" variant="subtle" class="w-full justify-center">
        {{ __('buttons.add_item') }}
    </flux:button>
</div> 