/**
 * Form Builder Alpine.js Module
 * Handles all client-side interactivity for the form builder system
 */

// Initialize the form builder store immediately if Alpine is available
function initializeFormBuilder() {
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js not available, retrying in 100ms');
        setTimeout(initializeFormBuilder, 100);
        return;
    }

    // Check if store already exists
    if (Alpine.store('formBuilder')) {
        console.log('Form Builder store already exists');
        return;
    }

    console.log('Initializing Form Builder store...');

    // Global store for form builder state
    Alpine.store('formBuilder', {
        // Current editing state
        editingElementId: null,
        editingElementState: {},
        
        // Auto-save state
        hasUnsavedChanges: false,
        autoSaveTimeout: null,
        autoSaveInterval: 30000, // 30 seconds
        
        // Element order state
        elementOrder: [],
        
        // Initialize the store
        init() {
            console.log('Form Builder store init() called');
            this.startAutoSave();
        },
        
        // Set editing element
        setEditingElement(elementId, elementState = {}) {
            console.log('Setting editing element:', elementId, elementState);
            
            // Save current element if switching
            if (this.editingElementId && this.editingElementId !== elementId) {
                this.saveCurrentElement();
            }
            
            this.editingElementId = elementId;
            this.editingElementState = { ...elementState };
            this.hasUnsavedChanges = false;
            
            // Dispatch event for other components
            this.$dispatch('element-editing-started', {
                elementId: this.editingElementId,
                elementState: this.editingElementState
            });
        },
        
        // Update element state
        updateElementState(path, value) {
            if (!this.editingElementId) return;
            
            console.log('Updating element state:', path, value);
            
            // Update nested property using dot notation
            const keys = path.split('.');
            let current = this.editingElementState;
            
            for (let i = 0; i < keys.length - 1; i++) {
                if (!current[keys[i]]) {
                    current[keys[i]] = {};
                }
                current = current[keys[i]];
            }
            
            current[keys[keys.length - 1]] = value;
            this.hasUnsavedChanges = true;
            
            // Trigger auto-save
            this.scheduleAutoSave();
            
            // Dispatch event for real-time preview updates
            this.$dispatch('element-state-updated', {
                elementId: this.editingElementId,
                path: path,
                value: value,
                elementState: this.editingElementState
            });
        },
        
        // Save current element
        saveCurrentElement() {
            if (!this.editingElementId || !this.hasUnsavedChanges) return;
            
            console.log('Saving current element:', this.editingElementId);
            
            // Call Livewire method if available
            if (this.$wire) {
                this.$wire.call('saveElementDraft', {
                    elementId: this.editingElementId,
                    state: this.editingElementState
                });
            }
            
            this.hasUnsavedChanges = false;
        },
        
        // Schedule auto-save
        scheduleAutoSave() {
            if (this.autoSaveTimeout) {
                clearTimeout(this.autoSaveTimeout);
            }
            
            this.autoSaveTimeout = setTimeout(() => {
                if (this.hasUnsavedChanges) {
                    this.saveCurrentElement();
                }
            }, 2000); // 2 second debounce
        },
        
        // Start periodic auto-save
        startAutoSave() {
            setInterval(() => {
                if (this.hasUnsavedChanges) {
                    this.saveCurrentElement();
                }
            }, this.autoSaveInterval);
        },
        
        // Update element order
        updateElementOrder(newOrder) {
            this.elementOrder = newOrder;
            if (this.$wire) {
                this.$wire.call('handleReorder', newOrder);
            }
        }
    });

    // Element component data
    Alpine.data('formElementComponent', (elementId, initialData = {}) => ({
        elementId,
        data: { ...initialData },
        isEditing: false,
        
        init() {
            console.log('Form element component initialized:', elementId);
            
            // Listen for element editing events
            this.$watch('$store.formBuilder.editingElementId', (newId) => {
                this.isEditing = newId === this.elementId;
                console.log('Element editing state changed:', this.elementId, this.isEditing);
                
                if (this.isEditing) {
                    // Sync with store state
                    this.data = { ...this.$store.formBuilder.editingElementState };
                }
            });
            
            // Listen for state updates from other components
            this.$el.addEventListener('element-state-updated', (event) => {
                if (event.detail.elementId === this.elementId) {
                    this.data = { ...event.detail.elementState };
                }
            });
        },
        
        // Update element data
        updateData(path, value) {
            this.data = this.setNestedValue(this.data, path, value);
            
            if (this.isEditing) {
                this.$store.formBuilder.updateElementState(path, value);
            }
        },
        
        // Helper to set nested object values
        setNestedValue(obj, path, value) {
            const keys = path.split('.');
            const result = { ...obj };
            let current = result;
            
            for (let i = 0; i < keys.length - 1; i++) {
                if (!current[keys[i]]) {
                    current[keys[i]] = {};
                }
                current = current[keys[i]];
            }
            
            current[keys[keys.length - 1]] = value;
            return result;
        },
        
        // Start editing this element
        startEditing() {
            this.$store.formBuilder.setEditingElement(this.elementId, this.data);
        }
    }));

    // Form builder editor component data
    Alpine.data('formBuilderEditor', () => ({
        init() {
            console.log('Form builder editor component initialized');
            
            // Listen for editing state changes
            this.$watch('$store.formBuilder.editingElementId', (elementId) => {
                console.log('Editing element ID changed:', elementId);
                if (elementId && this.$wire) {
                    // Load element data from Livewire
                    this.$wire.call('loadElementData', { elementId });
                }
            });
            
            // Listen for element data updates from Livewire
            if (this.$wire) {
                this.$wire.on('element-data-loaded', (event) => {
                    console.log('Element data loaded:', event);
                    this.$store.formBuilder.setEditingElement(
                        event.elementId,
                        event.elementState
                    );
                });
            }
        },
        
        // Save draft manually
        saveDraft() {
            console.log('Manual save draft called');
            this.$store.formBuilder.saveCurrentElement();
        },
        
        // Cancel editing
        cancelEditing() {
            console.log('Cancel editing called');
            this.$store.formBuilder.setEditingElement(null, {});
        }
    }));
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeFormBuilder);

// Also try to initialize immediately in case DOM is already ready
initializeFormBuilder(); 