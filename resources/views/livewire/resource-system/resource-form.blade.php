<div class="max-w-3xl">
    <div>
        <form wire:submit.prevent="save">
            <div class="space-y-6">
                @foreach ($fields as $field)
                    <div class="py-2">
                        @php
                            $attributes = new \Illuminate\View\ComponentAttributeBag();
                            
                            $wireModel = 'wire:model.defer';
                            if (method_exists($field, 'isReactive') && $field->isReactive()) {
                                $wireModel = 'wire:model.live';
                            }

                            $attributes = $attributes->merge([
                                $wireModel => 'data.' . $field->getName(),
                                'id' => $field->getName(),
                                'label' => $field->getLabel(),
                                'placeholder' => $field->getPlaceholder(),
                                'description' => $field->getHelpText(),
                                'badge' => $field->isRequired() ? __('labels.required') : null,
                            ]);
                        @endphp
                        
                        @if ($field instanceof \App\Services\ResourceSystem\Fields\Text)
                            <flux:input
                                type="{{ $field->getType() }}"
                                {{ $attributes }}
                            />
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Textarea)
                            <flux:textarea
                                rows="{{ $field->getRows() }}"
                                {{ $attributes }}
                            />
                        @elseif ($field instanceof \App\Services\ResourceSystem\Fields\Select)
                            <flux:select
                                {{ $attributes->except(['placeholder']) }}
                                placeholder="{{ $field->getPlaceholder() }}"
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
                                    {{ $attributes->except(['label', 'placeholder', 'description', 'badge']) }}
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
