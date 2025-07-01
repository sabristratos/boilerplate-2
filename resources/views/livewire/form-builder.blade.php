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
/* Sortable styles */
.sortable-ghost {
    opacity: 0.5 !important;
    background-color: rgb(255 130 16 / 0.1) !important;
    border: 2px dashed rgb(255 130 16) !important;
}

/* Drag and drop visual feedback */
.dragging {
    opacity: 0.5 !important;
    transform: rotate(2deg) scale(0.95) !important;
    transition: all 0.2s ease !important;
}

.drag-over {
    background-color: rgb(255 130 16 / 0.05) !important;
    border-color: rgb(255 130 16) !important;
    border-style: dashed !important;
}

/* Element card hover effects */
.element-card {
    transition: all 0.2s ease;
}

.element-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.element-card:active {
    transform: translateY(0) scale(0.98);
}

/* Responsive grid system */
.responsive-grid-container {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    width: 100%;
}

.responsive-grid-item {
    min-height: 60px;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .responsive-grid-container {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .responsive-grid-item {
        grid-column: span 1 !important;
    }
}

/* Tablet responsive adjustments */
@media (min-width: 769px) and (max-width: 1024px) {
    .responsive-grid-container {
        gap: 0.875rem;
    }
}
</style>
</div>


