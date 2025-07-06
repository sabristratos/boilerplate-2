@props(['elementTypes', 'settings', 'tab', 'availablePrebuiltForms'])
<div class="flex-1 overflow-y-auto p-4">
    <flux:tab.group wire:model.live="tab">
        <flux:tabs>
            <flux:tab name="toolbox">{{ __('messages.forms.form_builder_interface.toolbox') }}</flux:tab>
            <flux:tab name="settings">{{ __('messages.forms.form_builder_interface.global_settings') }}</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="toolbox" class="!p-0">
            <div class="p-4">
<<<<<<< HEAD
                <flux:heading size="lg" class="mb-4">Form Elements</flux:heading>
                <flux:text variant="subtle" class="mb-6">
                    Drag and drop elements to build your form
                </flux:text>
                
                <div class="space-y-3">
=======
                <flux:heading size="lg" class="mb-4">{{ __('messages.forms.form_builder_interface.toolbox') }}</flux:heading>
                <div class="mb-4">
                    <flux:select :label="__('messages.forms.form_builder_interface.load_prebuilt_form')" wire:change="loadPrebuiltForm($event.target.value)">
                        <option value="">{{ __('messages.forms.form_builder_interface.select_prebuilt_form') }}</option>
                        @foreach($availablePrebuiltForms as $prebuilt)
                            <option value="{{ get_class($prebuilt) }}">{{ $prebuilt->getName() }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="space-y-2">
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
                    @foreach($elementTypes as $elementType)
                    <div
                        class="element-card group relative p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-move hover:border-primary-300 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/20 transition-all duration-200"
                        draggable="true"
                        @dragstart="
                            event.dataTransfer.setData('type', '{{ $elementType->value }}');
                            event.target.classList.add('dragging');
                        "
                        @dragend="event.target.classList.remove('dragging')"
                        title="{{ $elementType->getDescription() }}"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                                    <flux:icon name="{{ $elementType->getIcon() }}" class="size-5 text-primary-600 dark:text-primary-400" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">
                                    {{ $elementType->getLabel() }}
                                </flux:heading>
                                <flux:text variant="subtle" class="text-xs mt-1">
                                    {{ $elementType->getDescription() }}
                                </flux:text>
                            </div>
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <flux:icon name="arrow-up-tray" class="size-4 text-zinc-400" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="settings" class="!p-0">
<<<<<<< HEAD
            <div class="p-4 space-y-4">
                <flux:heading size="lg" class="mb-4">Form Settings</flux:heading>
                <flux:input 
                    wire:model.live="settings.backgroundColor" 
                    type="color" 
                    label="Background Color" 
                />
                <flux:input 
                    wire:model.live="settings.defaultFont" 
                    label="Default Font Family" 
                    placeholder="e.g., Inter, sans-serif" 
                />
=======
            <div class="space-y-4 mt-4">
                <flux:input wire:model.live="settings.backgroundColor" type="color" :label="__('messages.forms.form_builder_interface.background_color')" />
                <flux:input wire:model.live="settings.defaultFont" :label="__('messages.forms.form_builder_interface.default_font')" :placeholder="__('messages.forms.form_builder_interface.default_font_placeholder')" />
>>>>>>> 3d646ebc8597a7b3e698f9f41fc701b941fde20d
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div> 
