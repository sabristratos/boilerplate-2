<div>
    <div class="mb-4">
        <a href="{{ route('admin.forms.index') }}" wire:navigate class="text-sm text-zinc-500 hover:text-zinc-700">
            &larr; Back to forms
        </a>
    </div>

    <form wire:submit.prevent="saveForm" class="mb-6 pb-6 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-4">
            <flux:heading>
                Edit Form: {{ $formState['name'] ?? '' }}
            </flux:heading>
            <div class="flex items-center gap-4">
                @if(count($this->availableLocales) > 1)
                    <flux:button.group>
                        @foreach($this->availableLocales as $localeCode => $localeName)
                            <flux:button
                                type="button"
                                wire:click="switchLocale('{{ $localeCode }}')"
                                variant="{{ $activeLocale === $localeCode ? 'primary' : 'ghost' }}"
                            >
                                {{ $localeName }}
                            </flux:button>
                        @endforeach
                    </flux:button.group>
                @endif
                <flux:button type="submit" variant="primary">Save Form</flux:button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input wire:model.lazy="formState.name" label="Form Name" />
            <flux:input wire:model.lazy="formState.title.{{ $activeLocale }}" label="Title" />
            <div class="md:col-span-2">
                <flux:textarea wire:model.lazy="formState.description.{{ $activeLocale }}" label="Description" />
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" x-data="{ breakpoint: $wire.entangle('breakpoint') }">
        {{-- Form Canvas --}}
        <div
            class="md:col-span-8"
            x-data="formBuilderCanvas({
                aligns: $wire.entangle('formState.submit_button_options.align')
            })"
        >
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Form Fields</h3>
                <flux:button.group>
                    <template x-if="breakpoint === 'desktop'">
                        <flux:button icon="computer-desktop" variant="primary" tooltip="Desktop"/>
                    </template>
                    <template x-if="breakpoint !== 'desktop'">
                        <flux:button x-on:click.prevent="breakpoint = 'desktop'" icon="computer-desktop" variant="ghost" tooltip="Desktop"/>
                    </template>
                    <template x-if="breakpoint === 'tablet'">
                        <flux:button icon="device-tablet" variant="primary" tooltip="Tablet"/>
                    </template>
                    <template x-if="breakpoint !== 'tablet'">
                        <flux:button x-on:click.prevent="breakpoint = 'tablet'" icon="device-tablet" variant="ghost" tooltip="Tablet"/>
                    </template>
                    <template x-if="breakpoint === 'mobile'">
                        <flux:button icon="device-phone-mobile" variant="primary" tooltip="Mobile"/>
                    </template>
                    <template x-if="breakpoint !== 'mobile'">
                        <flux:button x-on:click.prevent="breakpoint = 'mobile'" icon="device-phone-mobile" variant="ghost" tooltip="Mobile"/>
                    </template>
                </flux:button.group>
            </div>

            <div
                :class="{
                    'max-w-full': breakpoint === 'desktop',
                    'max-w-2xl': breakpoint === 'tablet',
                    'max-w-md': breakpoint === 'mobile'
                }"
                class="mx-auto transition-all duration-300 ease-in-out"
            >
                <div
                    class="grid grid-cols-12 gap-6"
                    x-sort
                    x-sort:config="{
                        pull: false,
                        onEnd: (event) => {
                            const orderedIds = Array.from(event.target.children)
                                .map(child => child.getAttribute('x-sort:item'))
                                .filter(Boolean);
                            $wire.updateFieldOrder(orderedIds);
                        }
                    }"
                >
                    @forelse($form->fields as $field)
                        <div x-sort:item="{{ $field->id }}" wire:key="field-{{ $field->id }}"
                             :class="{
                                'col-span-12': breakpoint === 'mobile' || (breakpoint === 'tablet' && '{{ data_get($field, 'layout_options.tablet', 'full') }}' === 'full') || (breakpoint === 'desktop' && '{{ data_get($field, 'layout_options.desktop', 'full') }}' === 'full'),
                                'col-span-6': (breakpoint === 'tablet' && '{{ data_get($field, 'layout_options.tablet') }}' === '1/2') || (breakpoint === 'desktop' && '{{ data_get($field, 'layout_options.desktop') }}' === '1/2'),
                                'col-span-4': breakpoint === 'desktop' && '{{ data_get($field, 'layout_options.desktop') }}' === '1/3'
                             }"
                             class="relative group {{ $selectedField && $selectedField->id === $field->id ? 'bg-primary-100/50 dark:bg-primary-900/10 rounded-lg' : '' }}">
                            <div class="p-4 rounded-lg">
                                <livewire:dynamic-component
                                    :component="$this->getPreviewComponent($field)"
                                    :fieldId="$field->id"
                                    wire:key="preview-{{ $field->id }}"
                                />
                                <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <flux:button.group>
                                        <flux:button wire:click.stop="selectField({{ $field->id }})" icon="pencil" variant="ghost" size="xs" />
                                        <flux:button wire:click.stop="confirmDelete({{ $field->id }})" icon="trash" variant="ghost" size="xs" />
                                        <flux:button x-sort:handle icon="grip-vertical" variant="ghost" size="xs" class="cursor-move" />
                                    </flux:button.group>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 col-span-12">
                            <flux:icon name="document-plus" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No fields</h3>
                            <p class="mt-1 text-sm text-gray-500">Add fields from the sidebar to get started.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Submit Button Preview --}}
                <div class="mt-6 p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    @php
                        $submitButtonLabel = data_get($formState, "submit_button_options.label.{$activeLocale}", 'Submit');
                    @endphp

                    <div class="flex" x-bind:class="alignmentClass">
                        <flux:button type="button" variant="primary" x-bind:class="{'w-full': isFullWidth}">
                            {{ $submitButtonLabel }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="md:col-span-4" x-data>
            <flux:tab.group>
                <flux:tabs wire:model.live="activeTab">
                    <flux:tab name="fields">Fields</flux:tab>
                    <flux:tab name="settings">Settings</flux:tab>
                </flux:tabs>

                {{-- Add Fields Tab --}}
                <flux:tab.panel name="fields" class="p-6 border border-gray-200 dark:border-gray-700 rounded-b-lg border-t-0">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($this->fieldTypes as $type)
                            <flux:button wire:click="addField('{{ $type->getName() }}')" type="button" class="w-full justify-center">
                                {{ $type->getLabel() }}
                            </flux:button>
                        @endforeach
                    </div>
                </flux:tab.panel>

                {{-- Form Settings Tab --}}
                <flux:tab.panel name="settings" class="p-6 border border-gray-200 dark:border-gray-700 rounded-b-lg border-t-0">
                    <div class="space-y-6">
                        <flux:input wire:model.lazy="formState.recipient_email" label="Recipient Email" />
                        <flux:textarea wire:model.lazy="formState.success_message.{{ $activeLocale }}" label="Success Message" />
                        <flux:switch wire:model.lazy="formState.send_notification" label="Send notification on submission" />
                        <flux:switch wire:model.lazy="formState.has_captcha" label="Enable Captcha" />

                        <div class="space-y-4 pt-6 border-t dark:border-gray-800">
                            <div class="flex justify-between items-center">
                                <h4 class="text-base font-medium text-gray-900 dark:text-white">Submit Button</h4>
                            </div>

                            <flux:input wire:model.lazy="formState.submit_button_options.label.{{ $activeLocale }}" label="Button Text" placeholder="Submit" />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alignment (Desktop)</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'left')"
                                        icon="bars-3-bottom-left"
                                        tooltip="Left"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'center')"
                                        icon="bars-3"
                                        tooltip="Center"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'right')"
                                        icon="bars-3-bottom-right"
                                        tooltip="Right"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'full')"
                                        icon="arrows-right-left"
                                        tooltip="Full Width"
                                    />
                                </flux:button.group>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alignment (Tablet)</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'left')"
                                        icon="bars-3-bottom-left"
                                        tooltip="Left"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'center')"
                                        icon="bars-3"
                                        tooltip="Center"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'right')"
                                        icon="bars-3-bottom-right"
                                        tooltip="Right"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'full')"
                                        icon="arrows-right-left"
                                        tooltip="Full Width"
                                    />
                                </flux:button.group>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alignment (Mobile)</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'left')"
                                        icon="bars-3-bottom-left"
                                        tooltip="Left"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'center')"
                                        icon="bars-3"
                                        tooltip="Center"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'right')"
                                        icon="bars-3-bottom-right"
                                        tooltip="Right"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'full')"
                                        icon="arrows-right-left"
                                        tooltip="Full Width"
                                    />
                                </flux:button.group>
                            </div>
                        </div>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
        </div>
    </div>

    {{-- Field Settings Flyout --}}
    @if($selectedField)
        <flux:modal name="edit-field-modal" variant="flyout" class="w-full md:w-1/3" @close="$wire.deselectField()">
            <div class="space-y-6">
                @if(count($this->availableLocales) > 1)
                    <div class="flex justify-end">
                        <flux:button.group>
                            @foreach($this->availableLocales as $localeCode => $localeName)
                                <flux:button
                                    wire:click="switchLocale('{{ $localeCode }}')"
                                    variant="{{ $activeLocale === $localeCode ? 'primary' : 'ghost' }}"
                                >
                                    {{ $localeName }}
                                </flux:button>
                            @endforeach
                        </flux:button.group>
                    </div>
                @endif
                <div>
                    <flux:heading size="lg">Edit Field</flux:heading>
                    <flux:text class="mt-1">Editing {{ $selectedField->type->value }} field.</flux:text>
                </div>

                <flux:tab.group>
                    <flux:tabs>
                        <flux:tab name="general" wire:click="$set('activeFieldTab', 'general')" :current="$activeFieldTab === 'general'">General</flux:tab>
                        <flux:tab name="appearance" wire:click="$set('activeFieldTab', 'appearance')" :current="$activeFieldTab === 'appearance'">Appearance</flux:tab>
                    </flux:tabs>
                    <flux:tab.panel name="general" class="py-6">
                        <div class="space-y-6">
                            <flux:field label="Field Type">
                                <p class="font-semibold">{{ $selectedField->type->getLabel() }}</p>
                            </flux:field>

                            <div class="grid grid-cols-2 gap-4 items-start">
                                <flux:input wire:model.debounce.500ms="fieldData.label" label="Label" />
                                <flux:input wire:model="fieldData.name" label="Name"
                                description:trailing="A unique name for this field (snake_case)." />
                            </div>

                            <flux:input wire:model.lazy="fieldData.placeholder" label="Placeholder" />

                            @if(in_array($selectedField->type, [\App\Enums\FormFieldType::SELECT, \App\Enums\FormFieldType::RADIO, \App\Enums\FormFieldType::CHECKBOX]))
                                @include('livewire.forms.partials._repeater', [
                                    'label' => 'Options',
                                    'description' => 'Add options for the user to select from.',
                                    'items' => $fieldData['options'] ?? [],
                                    'wireModel' => 'fieldData.options',
                                    'wireModelKey' => 'options',
                                ])
                            @endif

                            <div>
                                <label class="flux-label">Validation Rules</label>
                                <flux:checkbox.group wire:model.live="selectedRules" variant="pills" class="mt-2">
                                    @foreach($this->predefinedRulesWithTooltips as $rule => $tooltip)
                                        <flux:tooltip :content="$tooltip">
                                            <flux:checkbox value="{{ $rule }}" label="{{ ucfirst($rule) }}" />
                                        </flux:tooltip>
                                    @endforeach
                                </flux:checkbox.group>
                            </div>

                            <div>
                                <flux:textarea wire:model.debounce.500ms="fieldData.validation_rules" />
                                <flux:text variant="subtle" size="sm" class="mt-1">
                                    Enter validation rules, separated by pipes (|).
                                </flux:text>
                            </div>
                        </div>
                    </flux:tab.panel>
                    <flux:tab.panel name="appearance" class="py-6">
                        <div class="space-y-6">
                            @php
                                $fieldType = $this->fieldTypeManager->find($selectedField->type->value);
                                $options = $fieldType ? $fieldType->getComponentOptions() : [];
                            @endphp

                            @foreach ($options as $key => $option)
                                @if ($option['type'] === 'boolean')
                                    <flux:switch wire:model="fieldComponentOptions.{{ $key }}" label="{{ $option['label'] }}" />
                                @elseif ($option['type'] === 'string')
                                    <flux:input wire:model="fieldComponentOptions.{{ $key }}" label="{{ $option['label'] }}" />
                                @elseif ($option['type'] === 'number')
                                    <flux:input type="number" wire:model="fieldComponentOptions.{{ $key }}" label="{{ $option['label'] }}" />
                                @elseif ($option['type'] === 'select')
                                    <flux:select wire:model="fieldComponentOptions.{{ $key }}" label="{{ $option['label'] }}">
                                        @foreach ($option['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @endif
                            @endforeach

                            <div class="space-y-4 pt-6 border-t dark:border-gray-800">
                                <h4 class="text-base font-medium text-gray-900 dark:text-white">Layout</h4>
                                <flux:select wire:model="fieldData.layout_options.desktop" label="Desktop Width">
                                    <flux:select.option value="full">Full</flux:select.option>
                                    <flux:select.option value="1/2">1/2</flux:select.option>
                                    <flux:select.option value="1/3">1/3</flux:select.option>
                                </flux:select>
                                <flux:select wire:model="fieldData.layout_options.tablet" label="Tablet Width">
                                    <flux:select.option value="full">Full</flux:select.option>
                                    <flux:select.option value="1/2">1/2</flux:select.option>
                                </flux:select>
                                <flux:select wire:model="fieldData.layout_options.mobile" label="Mobile Width">
                                    <flux:select.option value="full">Full</flux:select.option>
                                </flux:select>
                            </div>
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>

            <div class="pt-6 border-t dark:border-gray-800 flex justify-between items-center mt-auto">
                <flux:button type="button" wire:click="confirmDelete({{ $selectedField->id }})" variant="danger">Delete</flux:button>
                <div>
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost" class="mr-2">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="button" wire:click="saveField" variant="primary">Save Field</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <flux:modal name="confirm-delete-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete field?</flux:heading>
                <flux:text class="mt-2">
                    Are you sure you want to delete this field? This action cannot be undone.
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteField" variant="danger">Delete field</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
