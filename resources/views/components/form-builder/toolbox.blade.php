@props(['elementTypes', 'availablePrebuiltForms', 'settings', 'tab'])
<div class="flex-1 overflow-y-auto p-4">
    <flux:tab.group wire:model.live="tab">
        <flux:tabs>
            <flux:tab name="toolbox">Toolbox</flux:tab>
            <flux:tab name="settings">Global Settings</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="toolbox" class="!p-0">
            <div class="p-4">
                <flux:heading size="lg" class="mb-4">Toolbox</flux:heading>
                <div class="mb-4">
                    <flux:select label="Load Prebuilt Form" wire:change="loadPrebuiltForm($event.target.value)">
                        <option value="">-- Select a prebuilt form --</option>
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
                <flux:input wire:model.live="settings.backgroundColor" type="color" label="Background Color" />
                <flux:input wire:model.live="settings.defaultFont" label="Default Font Family" placeholder="e.g., Inter, sans-serif" />
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div> 