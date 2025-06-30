@props(['selectedElement', 'selectedElementIndex', 'selectedElementId', 'activeBreakpoint', 'availableValidationRules', 'availableIcons'])
<div class="w-96 bg-white dark:bg-zinc-800/50 border-s border-zinc-200 dark:border-zinc-700/50 overflow-y-auto">
    @if($selectedElement)
        <div class="p-4" wire:key="properties-{{ $selectedElementId }}">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <flux:heading size="lg">Properties</flux:heading>
                    <flux:text variant="subtle">
                        {{ \App\Enums\FormElementType::tryFrom($selectedElement['type'])->getLabel() }}
                    </flux:text>
                </div>
                <flux:button wire:click="$set('selectedElementId', null)" icon="x-mark" variant="ghost" size="sm" />
            </div>
            
            <x-form-builder.properties-tabs 
                :selectedElement="$selectedElement"
                :selectedElementIndex="$selectedElementIndex"
                :activeBreakpoint="$activeBreakpoint"
                :availableValidationRules="$availableValidationRules"
                :availableIcons="$availableIcons"
            />
        </div>
    @else
        <div class="flex items-center justify-center h-full">
            <div class="text-center text-zinc-500">
                <flux:icon name="cursor-arrow-rays" class="size-10 mx-auto" />
                <flux:heading>Select an element</flux:heading>
                <flux:text variant="subtle">Click on an element in the canvas to edit its properties.</flux:text>
            </div>
        </div>
    @endif
</div> 