<div
    class="flex h-screen bg-zinc-100 dark:bg-zinc-900 font-sans"
    x-data="{
        handleDrop(event) {
            const type = event.dataTransfer.getData('type');
            if (type) {
                $wire.addElement(type);
            }
        }
    }"
>
    <!-- Left Panel: Toolbox & Settings -->
    <div class="w-80 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col">
        <x-form-builder.header :form="$form" />
        <x-form-builder.toolbox 
            :elementTypes="$elementTypes" 
            :availablePrebuiltForms="$this->availablePrebuiltForms" 
            :settings="$settings" 
            :tab="$tab" 
        />
    </div>

    <!-- Center Panel: Canvas -->
    <div class="flex-1 flex flex-col">
        <x-form-builder.canvas-toolbar 
            :activeBreakpoint="$activeBreakpoint" 
            :isPreviewMode="$isPreviewMode" 
        />
        <x-form-builder.form-canvas 
            :elements="$elements" 
            :activeBreakpoint="$activeBreakpoint" 
            :isPreviewMode="$isPreviewMode" 
            :form="$form" 
            :renderedElements="$renderedElements" 
            :previewElements="$previewElements" 
            :selectedElementId="$selectedElementId"
        />
    </div>

    <!-- Right Panel: Properties -->
    <x-form-builder.properties-panel 
        :selectedElement="$this->selectedElement" 
        :selectedElementIndex="$this->selectedElementIndex" 
        :selectedElementId="$selectedElementId"
        :activeBreakpoint="$activeBreakpoint" 
        :availableValidationRules="$this->availableValidationRules" 
        :availableIcons="$this->availableIcons" 
    />
    <style>
.sortable-ghost {
    opacity: 0.5 !important;
    background-color: rgb(59 130 246 / 0.1) !important;
    border: 2px dashed rgb(59 130 246) !important;
}
</style>
</div>


