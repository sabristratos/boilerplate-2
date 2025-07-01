@props(['elementTypes', 'availablePrebuiltForms', 'settings', 'tab'])
<div class="flex-1 overflow-y-auto p-4">
    <flux:tab.group wire:model.live="tab">
        <flux:tabs>
            <flux:tab name="toolbox">{{ __('messages.forms.form_builder_interface.toolbox') }}</flux:tab>
            <flux:tab name="settings">{{ __('messages.forms.form_builder_interface.global_settings') }}</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="toolbox" class="!p-0">
            <div class="p-4">
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
                    @foreach($elementTypes as $elementType)
                    <div
                            class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-move hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                        draggable="true"
                            @dragstart="event.dataTransfer.setData('type', '{{ $elementType->value }}')"
                    >
                            <flux:button 
                                variant="ghost" 
                                class="w-full justify-start"
                                tooltip="{{ $elementType->getDescription() }}"
                            >
                                <flux:icon name="{{ $elementType->getIcon() }}" class="size-4 mr-2" />
                                {{ $elementType->getLabel() }}
                            </flux:button>
                    </div>
                @endforeach
                </div>
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="settings" class="!p-0">
            <div class="space-y-4 mt-4">
                <flux:input wire:model.live="settings.backgroundColor" type="color" :label="__('messages.forms.form_builder_interface.background_color')" />
                <flux:input wire:model.live="settings.defaultFont" :label="__('messages.forms.form_builder_interface.default_font')" :placeholder="__('messages.forms.form_builder_interface.default_font_placeholder')" />
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div> 
