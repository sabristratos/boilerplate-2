/**
 * Page Builder Alpine.js Module
 * Handles all client-side interactivity for the page builder system
 */

// Initialize the page builder store immediately if Alpine is available
function initializePageBuilder() {
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js not available, retrying in 100ms');
        setTimeout(initializePageBuilder, 100);
        return;
    }

    // Check if store already exists
    if (Alpine.store('pageBuilder')) {
        console.log('Page Builder store already exists');
        return;
    }

    console.log('Initializing Page Builder store...');

    // Global store for page builder state
    Alpine.store('pageBuilder', {
        // Current editing state
        editingBlockId: null,
        editingBlockState: {},
        editingBlockVisible: true,
        
        // Auto-save state
        hasUnsavedChanges: false,
        autoSaveTimeout: null,
        autoSaveInterval: 30000, // 30 seconds
        
        // Block order state
        blockOrder: [],
        
        // Initialize the store
        init() {
            console.log('Page Builder store init() called');
            this.startAutoSave();
        },
        
        // Set editing block
        setEditingBlock(blockId, blockState = {}, visible = true) {
            console.log('Setting editing block:', blockId, blockState);
            
            // Save current block if switching
            if (this.editingBlockId && this.editingBlockId !== blockId) {
                this.saveCurrentBlock();
            }
            
            this.editingBlockId = blockId;
            this.editingBlockState = { ...blockState };
            this.editingBlockVisible = visible;
            this.hasUnsavedChanges = false;
            
            // Dispatch event for other components
            this.$dispatch('block-editing-started', {
                blockId: this.editingBlockId,
                blockState: this.editingBlockState,
                visible: this.editingBlockVisible
            });
        },
        
        // Update block state
        updateBlockState(path, value) {
            if (!this.editingBlockId) return;
            
            console.log('Updating block state:', path, value);
            
            // Update nested property using dot notation
            const keys = path.split('.');
            let current = this.editingBlockState;
            
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
            this.$dispatch('block-state-updated', {
                blockId: this.editingBlockId,
                path: path,
                value: value,
                blockState: this.editingBlockState
            });
        },
        
        // Update block visibility
        updateBlockVisibility(visible) {
            if (!this.editingBlockId) return;
            
            console.log('Updating block visibility:', visible);
            
            this.editingBlockVisible = visible;
            this.hasUnsavedChanges = true;
            this.scheduleAutoSave();
            
            // Dispatch event for visibility updates
            this.$dispatch('block-visibility-updated', {
                blockId: this.editingBlockId,
                visible: visible
            });
        },
        
        // Cancel editing
        cancelEditing() {
            console.log('Cancelling editing');
            
            if (this.editingBlockId) {
                this.saveCurrentBlock();
            }
            
            this.editingBlockId = null;
            this.editingBlockState = {};
            this.editingBlockVisible = true;
            this.hasUnsavedChanges = false;
            
            // Dispatch event for other components
            this.$dispatch('block-editing-cancelled');
        },
        
        // Save current block
        saveCurrentBlock() {
            if (!this.editingBlockId || !this.hasUnsavedChanges) return;
            
            console.log('Saving current block:', this.editingBlockId);
            
            // Call Livewire method if available
            if (this.$wire) {
                this.$wire.call('saveBlockDraft', {
                    blockId: this.editingBlockId,
                    state: this.editingBlockState,
                    visible: this.editingBlockVisible
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
                this.saveCurrentBlock();
            }, 2000); // 2 second delay
        },
        
        // Start periodic auto-save
        startAutoSave() {
            setInterval(() => {
                if (this.hasUnsavedChanges) {
                    this.saveCurrentBlock();
                }
            }, this.autoSaveInterval);
        },
        
        // Update block order
        updateBlockOrder(newOrder) {
            this.blockOrder = newOrder;
            if (this.$wire) {
                this.$wire.call('updateBlockOrder', newOrder);
            }
        }
    });

    // Block component data
    Alpine.data('blockComponent', (blockId, initialData = {}) => ({
        blockId,
        data: { ...initialData },
        isEditing: false,
        
        init() {
            console.log('Block component initialized:', blockId);
            
            // Listen for block editing events
            this.$watch('$store.pageBuilder.editingBlockId', (newId) => {
                this.isEditing = newId === this.blockId;
                console.log('Block editing state changed:', this.blockId, this.isEditing);
                
                if (this.isEditing) {
                    // Sync with store state
                    this.data = { ...this.$store.pageBuilder.editingBlockState };
                }
            });
            
            // Listen for state updates from other components
            this.$el.addEventListener('block-state-updated', (event) => {
                if (event.detail.blockId === this.blockId) {
                    this.data = { ...event.detail.blockState };
                }
            });
            
            // Listen for visibility updates
            this.$el.addEventListener('block-visibility-updated', (event) => {
                if (event.detail.blockId === this.blockId) {
                    // Handle visibility change if needed
                }
            });
        },
        
        // Update block data
        updateData(path, value) {
            this.data = this.setNestedValue(this.data, path, value);
            
            if (this.isEditing) {
                this.$store.pageBuilder.updateBlockState(path, value);
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
        
        // Start editing this block
        startEditing() {
            console.log('Starting edit for block:', this.blockId);
            this.$store.pageBuilder.setEditingBlock(this.blockId, this.data, true);
        }
    }));

    // Block editor component data
    Alpine.data('blockEditor', () => ({
        init() {
            console.log('Block editor component initialized');
            
            // Listen for editing state changes
            this.$watch('$store.pageBuilder.editingBlockId', (blockId) => {
                console.log('Editing block ID changed:', blockId);
                if (blockId && this.$wire) {
                    // Load block data from Livewire
                    this.$wire.call('loadBlockData', { blockId });
                }
            });
            
            // Listen for block data updates from Livewire
            if (this.$wire) {
                this.$wire.on('block-data-loaded', (event) => {
                    console.log('Block data loaded:', event);
                    this.$store.pageBuilder.setEditingBlock(
                        event.blockId,
                        event.blockState,
                        event.blockVisible
                    );
                });
            }
        },
        
        // Save draft manually
        saveDraft() {
            console.log('Manual save draft called');
            this.$store.pageBuilder.saveCurrentBlock();
        },
        
        // Cancel editing
        cancelEditing() {
            console.log('Cancel editing called');
            this.$store.pageBuilder.cancelEditing();
        }
    }));

    // Block library component data
    Alpine.data('blockLibrary', () => ({
        searchQuery: '',
        selectedCategory: 'all',
        
        // Add block to page
        addBlock(blockType) {
            if (this.$wire) {
                this.$wire.call('addBlock', { type: blockType });
            }
        },
        
        // Filter blocks based on search and category
        get filteredBlocks() {
            if (!this.$wire || !this.$wire.availableBlocks) return [];
            
            return this.$wire.availableBlocks.filter(block => {
                const matchesSearch = block.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                     block.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesCategory = this.selectedCategory === 'all' || block.category === this.selectedCategory;
                
                return matchesSearch && matchesCategory;
            });
        }
    }));

    // Sortable blocks component
    Alpine.data('sortableBlocks', () => ({
        init() {
            // Initialize sortable functionality
            this.$nextTick(() => {
                this.initializeSortable();
            });
        },
        
        initializeSortable() {
            // This would integrate with a sortable library like SortableJS
            // For now, we'll use a simple implementation
            const container = this.$el;
            const items = container.querySelectorAll('[data-block-id]');
            
            items.forEach(item => {
                item.addEventListener('dragstart', this.handleDragStart.bind(this));
                item.addEventListener('dragover', this.handleDragOver.bind(this));
                item.addEventListener('drop', this.handleDrop.bind(this));
            });
        },
        
        handleDragStart(event) {
            event.dataTransfer.setData('text/plain', event.target.dataset.blockId);
        },
        
        handleDragOver(event) {
            event.preventDefault();
        },
        
        handleDrop(event) {
            event.preventDefault();
            const draggedId = event.dataTransfer.getData('text/plain');
            const targetId = event.target.closest('[data-block-id]')?.dataset.blockId;
            
            if (draggedId && targetId && draggedId !== targetId && this.$wire) {
                this.$wire.call('reorderBlocks', { draggedId, targetId });
            }
        }
    }));

    console.log('Page Builder store initialized successfully');
}

// Initialize immediately if Alpine is available
initializePageBuilder();

// Also listen for alpine:init event as backup
document.addEventListener('alpine:init', () => {
    console.log('Alpine init event fired');
    initializePageBuilder();
});

// Ensure store is available globally
window.ensurePageBuilderStore = () => {
    console.log('ensurePageBuilderStore called');
    initializePageBuilder();
};

// Additional initialization on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM ready, ensuring Page Builder store');
    setTimeout(initializePageBuilder, 100);
});

// Initialize on window load as well
window.addEventListener('load', () => {
    console.log('Window loaded, ensuring Page Builder store');
    setTimeout(initializePageBuilder, 200);
}); 