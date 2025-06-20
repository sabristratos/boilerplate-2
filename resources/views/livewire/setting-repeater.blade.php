<div class="space-y-4">
    @foreach ($items as $index => $item)
        <div wire:key="repeater-item-{{ $index }}" class="flex items-start space-x-2 p-3 border border-zinc-200 dark:border-zinc-700 rounded-md">
            <div class="flex-grow grid gap-4" style="grid-template-columns: repeat({{ count($subfields) }}, 1fr);">
                @foreach ($subfields as $key => $subfield)
                    <flux:input
                        wire:model.live="items.{{ $index }}.{{ $key }}"
                        label="{{ __($subfield['label']) }}"
                        type="{{ $subfield['type'] ?? 'text' }}"
                        placeholder="{{ __($subfield['label']) }}"
                    />
                @endforeach
            </div>
            <div class="pt-6">
                <flux:button wire:click="removeItem({{ $index }})" variant="danger" size="sm" icon="trash" tooltip="{{ __('buttons.remove') }}" />
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        <flux:button wire:click="addItem" variant="subtle" icon="plus">
            {{ __('buttons.add_item') }}
        </flux:button>
    </div>
</div> 