<div>
    <label class="text-sm font-medium">{{ $field->getLabel() }}</label>
    <div class="mt-2 space-y-4">
        @foreach($this->data[$field->getName()] ?? [] as $index => $row)
            <div class="p-4 border rounded-lg">
                <div class="grid grid-cols-1 gap-4">
                    @foreach($field->getFields() as $repeaterField)
                        <div>
                            @if ($repeaterField instanceof \App\Services\ResourceSystem\Fields\Text)
                                <flux:input
                                    type="{{ $repeaterField->getType() }}"
                                    id="{{ $field->getName() }}.{{ $index }}.{{ $repeaterField->getName() }}"
                                    wire:model.defer="data.{{ $field->getName() }}.{{ $index }}.{{ $repeaterField->getName() }}"
                                    label="{{ $repeaterField->getLabel() }}"
                                    placeholder="{{ $repeaterField->getPlaceholder() }}"
                                />
                            @elseif ($repeaterField instanceof \App\Services\ResourceSystem\Fields\Textarea)
                                <flux:textarea
                                    id="{{ $field->getName() }}.{{ $index }}.{{ $repeaterField->getName() }}"
                                    wire:model.defer="data.{{ $field->getName() }}.{{ $index }}.{{ $repeaterField->getName() }}"
                                    label="{{ $repeaterField->getLabel() }}"
                                    placeholder="{{ $repeaterField->getPlaceholder() }}"
                                />
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <flux:button wire:click="removeRepeaterRow('{{ $field->getName() }}', {{ $index }})" variant="danger" size="sm">Remove</flux:button>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        <flux:button wire:click="addRepeaterRow('{{ $field->getName() }}')">Add {{ Str::singular($field->getLabel()) }}</flux:button>
    </div>
</div> 