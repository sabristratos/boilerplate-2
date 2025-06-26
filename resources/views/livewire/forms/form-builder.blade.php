<div>
    <div class="grid grid-cols-12 h-screen">
        {{-- Form Canvas --}}
        <div class="col-span-8 p-8 overflow-y-auto">
            <div class="max-w-3xl mx-auto">
                <flux:heading size="xl">{{ $form->title }}</flux:heading>
                <flux:text class="mt-2">{{ $form->description }}</flux:text>
                <hr class="my-8">

                <div x-data="{}"
                     x-sort
                     x-sort:config="{
                        onEnd: (event) => {
                            const orderedIds = Array.from(event.target.children)
                                .map(child => child.getAttribute('x-sort:item'));
                            $wire.updateFieldOrder(orderedIds);
                        }
                     }"
                     class="space-y-4">
                    <div x-sort:container class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($form->fields->sortBy('sort_order') as $field)
                            <div x-sort:item="{{ $field->id }}" wire:key="field-{{ $field->id }}"
                                 class="relative group p-4 rounded-lg {{ $selectedField && $selectedField->id === $field->id ? 'bg-primary-100/50 dark:bg-primary-900/10' : '' }}">
                                <livewire:is
                                    component="{{ $this->getPreviewComponent($field) }}"
                                    :fieldId="$field->id"
                                    wire:key="preview-{{ $field->id }}"
                                />
                                <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                    <flux:button.group>
                                        <flux:button x-on:click.stop="$wire.selectField({{ $field->id }})" icon="pencil" variant="ghost" size="xs" />
                                        <flux:button wire:click.stop="confirmDelete({{ $field->id }})" icon="trash" variant="ghost" size="xs" />
                                        <flux:button x-sort:handle icon="grip-vertical" variant="ghost" size="xs" class="cursor-move" />
                                    </flux:button.group>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 border-2 border-dashed dark:border-gray-700 rounded-lg">
                                <flux:heading>Your form is empty</flux:heading>
                                <flux:text class="mt-2">Add fields from the sidebar to get started.</flux:text>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-span-4 bg-white dark:bg-gray-900 border-l dark:border-gray-800 overflow-y-auto">
            <flux:tab.group>
                <flux:tabs wire:model.live="activeTab">
                    <flux:tab name="fields">Fields</flux:tab>
                    <flux:tab name="settings">Settings</flux:tab>
                </flux:tabs>

                {{-- Add Fields Tab --}}
                <flux:tab.panel name="fields" class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($this->fieldTypes as $type)
                            <flux:button wire:click="addField('{{ $type->value }}')" type="button" class="w-full justify-center">
                                {{ ucfirst($type->value) }}
                            </flux:button>
                        @endforeach
                    </div>
                </flux:tab.panel>

                {{-- Form Settings Tab --}}
                <flux:tab.panel name="settings" class="p-6">
                    <div class="space-y-6">
                        <flux:input wire:model.debounce.500ms="form.name" label="Form Name" />
                        <flux:input wire:model.debounce.500ms="form.title" label="Title" />
                        <flux:textarea wire:model.debounce.500ms="form.description" label="Description" />
                        <hr class="dark:border-gray-700">
                        <flux:input wire:model.debounce.500ms="form.recipient_email" label="Recipient Email" />
                        <flux:textarea wire:model.debounce.500ms="form.success_message" label="Success Message" />
                        <flux:checkbox wire:model.debounce.500ms="form.send_notification" label="Send notification on submission" />
                        <div class="flex justify-end">
                            <flux:button wire:click="saveForm" variant="primary">Save Settings</flux:button>
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
                <div>
                    <flux:heading size="lg">Edit Field</flux:heading>
                    <flux:text class="mt-1">Editing {{ $selectedField->type->value }} field.</flux:text>
                </div>

                <div class="space-y-6">
                    <flux:field label="Field Type">
                        <p class="font-semibold">{{ $selectedField->type->getLabel() }}</p>
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model.live="fieldData.label" label="Label" />
                        <flux:input wire:model="fieldData.name" label="Name"
                                    description="A unique name for this field (snake_case)." />
                    </div>

                    <flux:input wire:model="fieldData.placeholder" label="Placeholder" />
                    <flux:checkbox wire:model="fieldData.is_required" label="Required" />

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

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model.live="min" label="Min" placeholder="e.g., 5" />
                        <flux:input wire:model.live="max" label="Max" placeholder="e.g., 255" />
                    </div>

                    <flux:textarea wire:model.live="fieldData.validation_rules"
                                   placeholder="Enter Laravel validation rules, separated by pipes (|)." />
                </div>

                <div class="pt-6 border-t dark:border-gray-800 flex justify-between items-center">
                    <flux:button wire:click="deleteField" variant="danger">Delete</flux:button>
                    <div>
                        <flux:modal.close>
                            <flux:button variant="ghost" class="mr-2">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button wire:click="saveField" variant="primary">Save Field</flux:button>
                    </div>
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