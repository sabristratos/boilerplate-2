<div>
    <form wire:submit.prevent="submit">
        <flux:heading>{{ $form->getTranslation('title', app()->getLocale()) }}</flux:heading>
        <flux:text variant="subtle" class="mt-2">{{ $form->getTranslation('description', app()->getLocale()) }}</flux:text>

        <div class="mt-4 space-y-4">
            @foreach($form->formFields as $field)
                <div class="relative group">
                    @if($preview)
                        <div class="absolute top-2 right-2 hidden group-hover:flex items-center gap-x-1 bg-white p-1 rounded-md border">
                             <flux:button size="xs" variant="ghost">
                                <flux:icon.grip-vertical class="w-4 h-4" />
                            </flux:button>
                            <flux:button wire:click="$dispatch('editField', { fieldId: {{ $field->id }} })" size="xs" variant="ghost">
                                <flux:icon.pencil class="w-4 h-4" />
                            </flux:button>
                            <flux:button wire:click="$dispatch('removeField', { fieldId: {{ $field->id }} })" size="xs" variant="ghost">
                                <flux:icon.trash class="w-4 h-4 text-red-500" />
                            </flux:button>
                        </div>
                    @endif
                    @switch($field->type)
                        @case('text')
                            <div>
                                <flux:input
                                    wire:model.defer="formData.{{ $field->name }}"
                                    label="{{ $field->getTranslation('label', app()->getLocale()) }}"
                                    placeholder="{{ $field->getTranslation('placeholder', app()->getLocale()) }}"
                                    :invalid="$errors->has('formData.'.$field->name)"
                                />
                                @error('formData.'.$field->name) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @break
                        @case('textarea')
                            <div>
                                <flux:textarea
                                    wire:model.defer="formData.{{ $field->name }}"
                                    label="{{ $field->getTranslation('label', app()->getLocale()) }}"
                                    placeholder="{{ $field->getTranslation('placeholder', app()->getLocale()) }}"
                                    :invalid="$errors->has('formData.'.$field->name)"
                                />
                                @error('formData.'.$field->name) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @break
                        @case('select')
                            <div>
                                <flux:select
                                    wire:model.defer="formData.{{ $field->name }}"
                                    label="{{ $field->getTranslation('label', app()->getLocale()) }}"
                                    placeholder="{{ $field->getTranslation('placeholder', app()->getLocale()) }}"
                                    :invalid="$errors->has('formData.'.$field->name)"
                                >
                                    @php
                                        $options = $field->getTranslation('options', app()->getLocale());
                                    @endphp
                                    @if(is_array($options))
                                        @foreach($options as $option)
                                            <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                                        @endforeach
                                    @endif
                                </flux:select>
                                @error('formData.'.$field->name) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @break
                        @case('section')
                            <div class="pt-4 pb-2">
                                <flux:separator text="{{ $field->getTranslation('label', app()->getLocale()) }}" />
                            </div>
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>

        @if(!$preview)
            <div class="mt-4">
                <flux:button type="submit">Submit</flux:button>
            </div>
        @endif
    </form>
</div>
