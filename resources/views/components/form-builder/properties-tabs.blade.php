@props(['selectedElement', 'selectedElementIndex', 'activeBreakpoint', 'availableValidationRules', 'availableIcons'])
<flux:tab.group wire:model.live="propertiesTab">
    <flux:tabs>
        <flux:tab name="basic" icon="pencil">Basic</flux:tab>
        <flux:tab name="advanced" icon="cog-6-tooth">Advanced</flux:tab>
        <flux:tab name="styling" icon="paint-brush">Styling</flux:tab>
    </flux:tabs>
    
    <flux:tab.panel name="basic" class="space-y-4">
        <x-form-builder.basic-properties 
            :selectedElement="$selectedElement"
            :selectedElementIndex="$selectedElementIndex"
        />
    </flux:tab.panel>
    
    <flux:tab.panel name="advanced" class="space-y-6">
        <x-form-builder.advanced-properties 
            :selectedElement="$selectedElement"
            :selectedElementIndex="$selectedElementIndex"
            :availableValidationRules="$availableValidationRules"
            :availableIcons="$availableIcons"
        />
    </flux:tab.panel>
    
    <flux:tab.panel name="styling" class="space-y-6">
        <x-form-builder.styling-properties 
            :selectedElement="$selectedElement"
            :selectedElementIndex="$selectedElementIndex"
            :activeBreakpoint="$activeBreakpoint"
        />
    </flux:tab.panel>
</flux:tab.group> 