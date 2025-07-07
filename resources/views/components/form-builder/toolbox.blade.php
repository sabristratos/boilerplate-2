@props(['elementTypes', 'settings', 'tab'])
<div class="flex-1 overflow-y-auto py-4">
    <flux:tab.group wire:model.live="tab">
        <flux:tabs>
            <flux:tab name="toolbox">{{ __('messages.forms.form_builder_interface.toolbox') }}</flux:tab>
            <flux:tab name="settings">{{ __('messages.forms.form_builder_interface.global_settings') }}</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="toolbox" class="!p-0">
            <div class="p-4">
                <flux:heading size="lg" class="mb-4">{{ __('messages.forms.form_builder_interface.toolbox') }}</flux:heading>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($elementTypes as $elementType)
                    <flux:tooltip content="{{ $elementType->getDescription() }}">
                        <div
                            class="element-card group relative p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-move hover:border-primary-300 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/20 transition-all duration-200"
                            draggable="true"
                            @dragstart="
                                event.dataTransfer.setData('type', '{{ $elementType->value }}');
                                event.target.classList.add('dragging');
                            "
                            @dragend="event.target.classList.remove('dragging')"
                        >
                            <div class="flex flex-col items-center text-center space-y-2">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                                        <flux:icon name="{{ $elementType->getIcon() }}" class="size-4 text-primary-600 dark:text-primary-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <flux:heading size="xs" class="text-zinc-900 dark:text-zinc-100">
                                        {{ $elementType->getLabel() }}
                                    </flux:heading>
                                </div>
                                <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:icon name="arrow-up-tray" class="size-3 text-zinc-400" />
                                </div>
                            </div>
                        </div>
                    </flux:tooltip>
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
