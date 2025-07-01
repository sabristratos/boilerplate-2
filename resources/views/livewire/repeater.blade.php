<div>
    <div class="space-y-4">
        @foreach ($items as $index => $item)
            <div wire:key="repeater-item-{{ $index }}" class="flex items-start space-x-2">
                <div class="grid flex-1 grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($subfields as $key => $field)
                        <div>
                            @switch($field['type'])
                                @case('select')
                                    <flux:select
                                        wire:model.live="items.{{ $index }}.{{ $key }}"
                                        label="{{ $field['label'] }}"
                                    >
                                        @foreach ($field['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    @break

                                @default
                                    <flux:input
                                        wire:model.live="items.{{ $index }}.{{ $key }}"
                                        label="{{ $field['label'] }}"
                                        type="{{ $field['type'] }}"
                                    />
                                    @break
                            @endswitch
                        </div>
                    @endforeach
                </div>
                <flux:button
                    class="mt-6"
                    wire:click="removeItem({{ $index }})"
                    icon="trash"
                    variant="ghost"
                />
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <flux:button
            wire:click="addItem"
            type="button"
            variant="outline"
            icon="plus"
        >
            {{ __('buttons.add') }}
        </flux:button>
    </div>
</div>
