<div class="max-w-3xl">
    <div>
        <form wire:submit.prevent="save">
            <div class="space-y-6">
                @foreach ($fields as $field)
                    <div class="py-2">
                        @if ($field instanceof \App\Services\ResourceSystem\Fields\Text)
                            <flux:input
                                type="{{ $field->getType() }}"
                                id="{{ $field->getName() }}"
                                wire:model.defer="data.{{ $field->getName() }}"
                                label="{{ $field->getLabel() }}"
                                placeholder="{{ $field->getPlaceholder() }}"
                                description="{{ $field->getHelpText() }}"
                                :badge="$field->isRequired() ? 'Required' : null"
                            />
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Textarea)
                            <flux:textarea
                                id="{{ $field->getName() }}"
                                wire:model.defer="data.{{ $field->getName() }}"
                                label="{{ $field->getLabel() }}"
                                placeholder="{{ $field->getPlaceholder() }}"
                                description="{{ $field->getHelpText() }}"
                                rows="{{ $field->getRows() }}"
                                :badge="$field->isRequired() ? 'Required' : null"
                            />
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Select)
                            <flux:select
                                id="{{ $field->getName() }}"
                                wire:model.defer="data.{{ $field->getName() }}"
                                label="{{ $field->getLabel() }}"
                                placeholder="{{ $field->getPlaceholder() }}"
                                description="{{ $field->getHelpText() }}"
                                :badge="$field->isRequired() ? 'Required' : null"
                                variant="listbox"
                                :multiple="$field->isMultiple()"
                            >
                                @foreach ($field->getOptions() as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Media)
                            <flux:field>
                                <flux:label>{{ $field->getLabel() }}</flux:label>
                                @if ($field->getHelpText())
                                    <flux:description>{{ $field->getHelpText() }}</flux:description>
                                @endif
                                <livewire:media-uploader :model="$model" collection="{{ $field->getName() }}" />
                            </flux:field>
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Rating)
                            <flux:field>
                                <flux:label>{{ $field->getLabel() }}</flux:label>
                                @if ($field->getHelpText())
                                    <flux:description>{{ $field->getHelpText() }}</flux:description>
                                @endif
                                <x-rating
                                    wire:model="data.{{ $field->getName() }}"
                                    :value="$data[$field->getName()] ?? 0"
                                />
                            </flux:field>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-6 flex justify-end">
                <flux:button
                    type="submit"
                    variant="primary"
                >
                    {{ __('buttons.save') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
