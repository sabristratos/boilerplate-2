<div>
    <div class="mb-4">
        <a href="{{ route('admin.forms.index') }}" wire:navigate class="text-sm text-zinc-500 hover:text-zinc-700">
            &larr; {{ __('forms.back_to_forms') }}
        </a>
    </div>

    <form wire:submit.prevent="saveForm" class="mb-6 pb-6 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center mb-4">
            <flux:heading>
                {{ __('forms.edit_form_title', ['name' => data_get($formState, "name.{$activeLocale}", $formState['name'][config('app.fallback_locale')] ?? '')]) }}
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
                <flux:button type="submit" variant="primary">{{ __('forms.save_form') }}</flux:button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input wire:model.lazy="formState.name.{{ $activeLocale }}" :label="__('forms.form_name')" />
            <flux:input wire:model.lazy="formState.title.{{ $activeLocale }}" :label="__('forms.form_title')" />
            <div class="md:col-span-2">
                <flux:textarea wire:model.lazy="formState.description.{{ $activeLocale }}" :label="__('forms.form_description')" />
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('forms.form_fields') }}</h3>
                <flux:button.group>
                    <template x-if="breakpoint === 'desktop'">
                        <flux:button icon="computer-desktop" variant="primary" :tooltip="__('forms.desktop')"/>
                    </template>
                    <template x-if="breakpoint !== 'desktop'">
                        <flux:button x-on:click.prevent="breakpoint = 'desktop'" icon="computer-desktop" variant="ghost" :tooltip="__('forms.desktop')"/>
                    </template>
                    <template x-if="breakpoint === 'tablet'">
                        <flux:button icon="device-tablet" variant="primary" :tooltip="__('forms.tablet')"/>
                    </template>
                    <template x-if="breakpoint !== 'tablet'">
                        <flux:button x-on:click.prevent="breakpoint = 'tablet'" icon="device-tablet" variant="ghost" :tooltip="__('forms.tablet')"/>
                    </template>
                    <template x-if="breakpoint === 'mobile'">
                        <flux:button icon="device-phone-mobile" variant="primary" :tooltip="__('forms.mobile')"/>
                    </template>
                    <template x-if="breakpoint !== 'mobile'">
                        <flux:button x-on:click.prevent="breakpoint = 'mobile'" icon="device-phone-mobile" variant="ghost" :tooltip="__('forms.mobile')"/>
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('forms.no_fields') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('forms.add_fields_to_start') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Submit Button Preview --}}
                <div class="mt-6 p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    @php
                        $submitButtonLabel = data_get($formState, "submit_button_options.label.{$activeLocale}", __('buttons.submit'));
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
                    <flux:tab name="fields">{{ __('forms.fields') }}</flux:tab>
                    <flux:tab name="settings">{{ __('forms.settings') }}</flux:tab>
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
                        <flux:input wire:model.lazy="formState.recipient_email" :label="__('forms.recipient_email')" />
                        <flux:textarea wire:model.lazy="formState.success_message.{{ $activeLocale }}" :label="__('forms.success_message')" />
                        <flux:switch wire:model.lazy="formState.send_notification" :label="__('forms.send_notification_on_submission')" />
                        <flux:switch wire:model.lazy="formState.has_captcha" :label="__('forms.enable_captcha')" />

                        <div class="space-y-4 pt-6 border-t dark:border-gray-800">
                            <div class="flex justify-between items-center">
                                <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ __('forms.submit_button') }}</h4>
                            </div>

                            <flux:input wire:model.lazy="formState.submit_button_options.label.{{ $activeLocale }}" :label="__('forms.button_text')" :placeholder="__('buttons.submit')" />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('forms.alignment_desktop') }}</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'left')"
                                        icon="bars-3-bottom-left"
                                        :tooltip="__('forms.align_left')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'center')"
                                        icon="bars-3"
                                        :tooltip="__('forms.align_center')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'right')"
                                        icon="bars-3-bottom-right"
                                        :tooltip="__('forms.align_right')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.desktop') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.desktop', 'full')"
                                        icon="arrows-right-left"
                                        :tooltip="__('forms.align_full_width')"
                                    />
                                </flux:button.group>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('forms.alignment_tablet') }}</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'left')"
                                        icon="bars-3-bottom-left"
                                        :tooltip="__('forms.align_left')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'center')"
                                        icon="bars-3"
                                        :tooltip="__('forms.align_center')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'right')"
                                        icon="bars-3-bottom-right"
                                        :tooltip="__('forms.align_right')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.tablet') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.tablet', 'full')"
                                        icon="arrows-right-left"
                                        :tooltip="__('forms.align_full_width')"
                                    />
                                </flux:button.group>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('forms.alignment_mobile') }}</label>
                                <flux:button.group class="mt-2">
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'left' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'left')"
                                        icon="bars-3-bottom-left"
                                        :tooltip="__('forms.align_left')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'center' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'center')"
                                        icon="bars-3"
                                        :tooltip="__('forms.align_center')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'right' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'right')"
                                        icon="bars-3-bottom-right"
                                        :tooltip="__('forms.align_right')"
                                    />
                                    <flux:button
                                        :variant="data_get($formState, 'submit_button_options.align.mobile') === 'full' ? 'primary' : 'ghost'"
                                        wire:click.prevent="$set('formState.submit_button_options.align.mobile', 'full')"
                                        icon="arrows-right-left"
                                        :tooltip="__('forms.align_full_width')"
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
        <flux:modal name="edit-field-modal" :dismissible="false">
            <div class="p-6">
                <flux:tabs wire:model.live="activeFieldTab">
                    <flux:tab name="general">{{ __('forms.general') }}</flux:tab>
                    @if($this->hasOptions)
                        <flux:tab name="options">{{ __('forms.options') }}</flux:tab>
                    @endif
                    <flux:tab name="layout">{{ __('forms.layout') }}</flux:tab>
                </flux:tabs>

                <flux:tab.panel name="general">
                    <div class="mt-6 space-y-4">
                        <flux:input wire:model.lazy="fieldData.label" :label="__('forms.label')" />
                        <flux:input wire:model.lazy="fieldData.name" :label="__('forms.field_name')" />
                        <flux:input wire:model.lazy="fieldData.placeholder" :label="__('forms.placeholder')" />
                        <flux:input wire:model.lazy="fieldData.validation_rules" :label="__('forms.validation_rules')" />
                    </div>
                </flux:tab.panel>

                @if($this->hasOptions)
                    <flux:tab.panel name="options">
                        <div class="mt-6 space-y-4">
                            @foreach($fieldData['options'] as $index => $option)
                                <div wire:key="option-{{ $index }}" class="flex items-center gap-2 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                    <div class="grid grid-cols-2 gap-2 flex-grow">
                                        <flux:input wire:model.lazy="fieldData.options.{{ $index }}.label" :label="__('forms.label')" />
                                        <flux:input wire:model.lazy="fieldData.options.{{ $index }}.value" :label="__('forms.value')" />
                                    </div>
                                    <flux:button wire:click="removeOption({{ $index }})" icon="trash" variant="ghost" class="text-danger-500" />
                                </div>
                            @endforeach

                            <flux:button wire:click="addOption" icon="plus" class="mt-4">
                                {{ __('forms.add_option') }}
                            </flux:button>
                        </div>
                    </flux:tab.panel>
                @endif

                <flux:tab.panel name="layout">
                    <div class="mt-6">
                        <!-- Layout options here -->
                    </div>
                </flux:tab.panel>
            </div>
            <div class="p-6 bg-zinc-50 dark:bg-zinc-800/50 flex justify-end gap-4">
                <flux:button wire:click="selectField(null)" variant="ghost">{{ __('buttons.cancel') }}</flux:button>
                <flux:button wire:click="saveField" variant="primary">{{ __('buttons.save') }}</flux:button>
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
