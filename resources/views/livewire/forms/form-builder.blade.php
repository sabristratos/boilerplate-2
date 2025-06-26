<div x-data>
    <div class="mb-4">
        <a href="{{ route('admin.forms.index') }}" wire:navigate class="text-sm text-zinc-500 hover:text-zinc-700">
            &larr; {{ __('forms.back_to_forms') }}
        </a>
    </div>

    <div class="flex items-center justify-between pb-4 border-b border-zinc-200">
        <div class="flex items-center gap-x-2">
            <h1 class="text-2xl font-semibold">{{ $form->name }}</h1>
        </div>
        <div class="flex items-center gap-x-2">
            <flux:button variant="primary" wire:click="save">
                {{ __('forms.save_form') }}
            </flux:button>
        </div>
    </div>

    <div class="grid flex-1 grid-cols-12 mt-6">
        <div class="col-span-3 min-h-full pr-8 border-r border-zinc-200">
            <flux:tab.group wire:model.live="activeTab">
                <flux:tabs>
                    <flux:tab name="fields">{{ __('forms.fields') }}</flux:tab>
                    <flux:tab name="settings">{{ __('forms.settings') }}</flux:tab>
                </flux:tabs>
                <flux:tab.panel name="fields" class="mt-6">
                    <div class="grid grid-cols-2 gap-2">
                        <flux:button wire:click="addField('text')" class="h-auto !p-0">
                            <div class="flex flex-col items-center justify-center p-3 space-y-1">
                                <flux:icon.document-text class="w-6 h-6" />
                                <span class="text-sm">{{ __('forms.add_text_input') }}</span>
                            </div>
                        </flux:button>
                        <flux:button wire:click="addField('textarea')" class="h-auto !p-0">
                            <div class="flex flex-col items-center justify-center p-3 space-y-1">
                                <flux:icon.document-text class="w-6 h-6" />
                                <span class="text-sm">{{ __('forms.add_textarea') }}</span>
                            </div>
                        </flux:button>
                        <flux:button wire:click="addField('select')" class="h-auto !p-0">
                            <div class="flex flex-col items-center justify-center p-3 space-y-1">
                                <flux:icon.list-bullet class="w-6 h-6" />
                                <span class="text-sm">{{ __('forms.add_select') }}</span>
                            </div>
                        </flux:button>
                        <flux:button wire:click="addField('section')" class="h-auto !p-0">
                            <div class="flex flex-col items-center justify-center p-3 space-y-1">
                                <flux:icon.minus class="w-6 h-6" />
                                <span class="text-sm">{{ __('forms.add_section') }}</span>
                            </div>
                        </flux:button>
                    </div>
                </flux:tab.panel>
                <flux:tab.panel name="settings" class="mt-6 space-y-4">
                    <flux:input
                        :id="$form->id.'_name'"
                        wire:model="name"
                        label="{{ __('forms.form_name') }}"
                        description="{{ __('forms.form_name_help') }}"
                    />
                    <div class="flex flex-col gap-y-4">
                        <flux:input
                            type="email"
                            :id="$form->id.'_recipient_email'"
                            wire:model="form.recipient_email"
                            label="{{ __('forms.form_recipient_email') }}"
                            description="{{ __('forms.form_recipient_email_help') }}"
                        />
                        <flux:checkbox
                            :id="$form->id.'_send_notification'"
                            wire:model="form.send_notification"
                            label="{{ __('forms.form_send_notification') }}"
                            description="{{ __('forms.form_send_notification_help') }}"
                        />
                    </div>
                    <flux:input
                        :id="$form->id.'_title'"
                        wire:model="form.title.{{ $activeLocale }}"
                        label="{{ __('forms.form_title') }}"
                    />
                    <flux:textarea
                        :id="$form->id.'_description'"
                        wire:model="form.description.{{ $activeLocale }}"
                        label="{{ __('forms.form_description') }}"
                    />
                    <flux:textarea
                        :id="$form->id.'_success_message'"
                        wire:model="form.success_message.{{ $activeLocale }}"
                        label="{{ __('forms.form_success_message') }}"
                    />
                </flux:tab.panel>
            </flux:tab.group>
        </div>
        <div class="col-span-9 pl-8">
            <div class="p-8 bg-gray-100 rounded-lg">
                <div class="max-w-md mx-auto">
                    <div class="p-6 bg-white border rounded-lg">
                        <livewire:frontend.form-display :form="$form" :preview="true" wire:key="form-display-{{ $form->id }}-{{ $form->formFields->count() }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($editingFieldId)
    <flux:modal name="edit-field-modal" variant="flyout">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Field</flux:heading>
                <flux:text class="mt-2">Make changes to this field.</flux:text>
            </div>
            <div class="space-y-4">
                <flux:input
                    :id="$editingFieldId.'_label'"
                    wire:model.live.debounce.500ms="editingFieldState.label.{{ $activeLocale }}"
                    label="{{ __('forms.label') }}"
                />
                @if($editingFieldState['type'] !== 'section')
                    <flux:input
                        :id="$editingFieldId.'_placeholder'"
                        wire:model.live.debounce.500ms="editingFieldState.placeholder.{{ $activeLocale }}"
                        label="{{ __('forms.placeholder') }}"
                    />
                @endif
                @if($editingFieldState['type'] === 'select')
                    <flux:textarea
                        :id="$editingFieldId.'_options'"
                        wire:model.live.debounce.500ms="editingFieldState.options.{{ $activeLocale }}"
                        label="{{ __('forms.options') }}"
                        description="One option per line."
                    />
                @endif
                @if($editingFieldState['type'] !== 'section')
                    <flux:checkbox
                        :id="$editingFieldId.'_is_required'"
                        wire:model.live.debounce.500ms="editingFieldState.is_required"
                        label="{{ __('forms.is_required') }}"
                    />
                @endif
            </div>
            <div class="flex">
                <flux:spacer />
                <flux:button wire:click="saveField" variant="primary">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
