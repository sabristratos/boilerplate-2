<div
    class="flex flex-col h-screen bg-zinc-100 dark:bg-zinc-900 font-sans form-builder"
    x-data="{
        ...formBuilderEditor(),
        
        // Handle drop events
        handleDrop(event) {
            const type = event.dataTransfer.getData('type');
            if (type) {
                $wire.addElement(type);
            }
        },
        
        // Handle element property updates
        handleElementUpdate(event) {
            // Dispatch event for other components to listen to
            this.$dispatch('form-element-updated', event);
        },
        
        // Update preview element in real-time
        updatePreviewElement(event) {
            const elementId = event.elementId;
            const previewContainer = document.querySelector(`[data-preview-element-id='${elementId}']`);
            if (previewContainer) {
                previewContainer.innerHTML = event.html;
            }
        },
        
        // Update edit element in real-time
        updateEditElement(event) {
            const elementId = event.elementId;
            const editContainer = document.querySelector(`[data-edit-element-id='${elementId}']`);
            if (editContainer) {
                editContainer.innerHTML = event.html;
            }
        }
    }"
    x-init="
        // Listen for element updates from Livewire
        $wire.on('element-updated', (event) => {
            handleElementUpdate(event);
        });
        
        $wire.on('preview-element-updated', (event) => {
            updatePreviewElement(event);
        });
        
        $wire.on('edit-element-updated', (event) => {
            updateEditElement(event);
        });
    "
>
    <!-- Unified Header -->
    <x-form-builder.header 
        :form="$form" 
        :activeBreakpoint="$activeBreakpoint" 
        :isPreviewMode="$isPreviewMode" 
        :hasUnsavedChanges="$this->hasChanges"
    />

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left Panel: Toolbox & Settings -->
        <div class="w-80 bg-white dark:bg-zinc-800/50 border-e border-zinc-200 dark:border-zinc-700/50 flex flex-col">
            <x-form-builder.toolbox 
                :elementTypes="$this->elementTypes" 
                :settings="$settings" 
                :tab="$tab" 
            />
        </div>

        <!-- Center Panel: Canvas -->
        <div class="flex-1 flex flex-col">
            <x-form-builder.form-canvas 
                :elements="$elements" 
                :activeBreakpoint="$activeBreakpoint" 
                :isPreviewMode="$isPreviewMode" 
                :form="$form" 
                :renderedElements="$this->renderedElements" 
                :selectedElementId="$selectedElementId"
            />
        </div>

        <!-- Right Panel: Properties -->
        <x-form-builder.properties-panel 
            :selectedElement="$selectedElement" 
            :selectedElementIndex="$selectedElementIndex" 
            :selectedElementId="$selectedElementId"
            :activeBreakpoint="$activeBreakpoint" 
            :availableValidationRules="$this->availableValidationRules" 
            :availableIcons="$this->availableIcons" 
        />
    </div>
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
    gap: 0.5rem;
    width: 100%;
}

.responsive-grid-item {
    min-height: 40px;
}

/* Compact form elements in form builder */
.form-canvas .responsive-grid-item .space-y-2 {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

.form-canvas .responsive-grid-item .space-y-2 > * + * {
    margin-top: 0.25rem;
}

/* Reduce padding on form elements in builder mode */
.form-canvas .responsive-grid-item input,
.form-canvas .responsive-grid-item textarea,
.form-canvas .responsive-grid-item select {
    padding-top: 0.375rem;
    padding-bottom: 0.375rem;
}

/* Dropdown overflow fix - allow select dropdowns to break out of containers */
select {
    z-index: 10;
}

/* Ensure dropdowns can overflow their containers */
.responsive-grid-item select,
.form-canvas select,
.form-builder select {
    position: relative;
    z-index: 50;
}

/* When select is focused/open, ensure it's above other elements */
.responsive-grid-item select:focus,
.form-canvas select:focus,
.form-builder select:focus {
    z-index: 100;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .responsive-grid-container {
        grid-template-columns: 1fr;
        gap: 0.375rem;
    }
    
    .responsive-grid-item {
        grid-column: span 1 !important;
    }
}

/* Tablet responsive adjustments */
@media (min-width: 769px) and (max-width: 1024px) {
    .responsive-grid-container {
        gap: 0.5rem;
    }
}
</style>
</div>


