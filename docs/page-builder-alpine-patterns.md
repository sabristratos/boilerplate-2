# Page Builder Alpine.js Patterns

This document outlines the client-side interaction patterns used in the page builder system, combining Alpine.js with Livewire for optimal performance and user experience.

## Overview

The page builder uses a hybrid approach where:
- **Alpine.js** handles immediate UI interactions and state management
- **Livewire** handles server-side operations and data persistence
- **Events** facilitate communication between components

## Core Architecture

### 1. Global Store (`pageBuilder`)

The global Alpine.js store manages the editing state:

```javascript
Alpine.store('pageBuilder', {
    editingBlockId: null,        // Currently editing block
    editingBlockState: {},       // Block data being edited
    editingBlockVisible: true,   // Block visibility
    hasUnsavedChanges: false,    // Auto-save tracking
    // ... other properties
});
```

### 2. Component Data Patterns

Each block component uses the `blockComponent` Alpine data:

```javascript
Alpine.data('blockComponent', (blockId, initialData = {}) => ({
    blockId,
    data: { ...initialData },
    isEditing: false,
    
    init() {
        // Listen for editing state changes
        this.$watch('$store.pageBuilder.editingBlockId', (newId) => {
            this.isEditing = newId === this.blockId;
        });
    },
    
    startEditing() {
        this.$store.pageBuilder.setEditingBlock(this.blockId, this.data, true);
    }
}));
```

## Event System

### Client-Side Events (Alpine.js)

Events are dispatched using `$dispatch()` and listened to with `@event-name`:

```javascript
// Dispatching events
this.$dispatch('block-editing-started', {
    blockId: this.editingBlockId,
    blockState: this.editingBlockState
});

// Listening for events
@block-editing-started.window="handleBlockEditStarted($event.detail)"
```

### Server-Side Events (Livewire)

Livewire events are handled with `#[On]` attributes:

```php
#[On('saveBlockDraft')]
public function saveBlockDraft($data): void
{
    // Handle saving block data
    $this->dispatch('block-draft-saved', ['blockId' => $data['blockId']]);
}
```

## Block Component Patterns

### Basic Block Structure

```blade
<div 
    x-data="blockComponent({{ $block->id }}, {{ json_encode($blockData) }})"
    data-block-id="{{ $block->id }}"
    class="block-preview-section"
>
    <!-- Block content -->
    <div @click="startEditing()">
        @include($blockClass->getFrontendView(), [
            'block' => $block, 
            'data' => 'data', 
            'alpine' => true
        ])
    </div>
</div>
```

### Block Content with Alpine.js

```blade
@props(['block', 'data' => [], 'alpine' => false])

<div 
    @if($alpine)
        x-data="{ 
            data: {{ json_encode($data) }},
            
            updateData(path, value) {
                this.data = this.setNestedValue(this.data, path, value);
                
                if (this.$store.pageBuilder && 
                    this.$store.pageBuilder.editingBlockId === {{ $block?->id ?? 'null' }}) {
                    this.$store.pageBuilder.updateBlockState(path, value);
                }
            }
        }"
    @endif
>
    <!-- Content that updates reactively -->
    <div x-html="data.content || ''"></div>
</div>
```

## State Management Flow

### 1. Starting Block Edit

```javascript
// User clicks edit button
startEditing() {
    this.$store.pageBuilder.setEditingBlock(this.blockId, this.data, true);
}

// Store updates and dispatches event
setEditingBlock(blockId, blockState, visible) {
    this.editingBlockId = blockId;
    this.editingBlockState = { ...blockState };
    this.$dispatch('block-editing-started', { blockId, blockState, visible });
}
```

### 2. Updating Block Data

```javascript
// User changes form field
updateData(path, value) {
    this.data = this.setNestedValue(this.data, path, value);
    
    if (this.isEditing) {
        this.$store.pageBuilder.updateBlockState(path, value);
    }
}

// Store updates and dispatches event
updateBlockState(path, value) {
    // Update nested property
    const keys = path.split('.');
    let current = this.editingBlockState;
    // ... update logic
    
    this.$dispatch('block-state-updated', {
        blockId: this.editingBlockId,
        path: path,
        value: value,
        blockState: this.editingBlockState
    });
}
```

### 3. Auto-Saving

```javascript
// Store schedules auto-save
scheduleAutoSave() {
    if (this.autoSaveTimeout) {
        clearTimeout(this.autoSaveTimeout);
    }
    
    this.autoSaveTimeout = setTimeout(() => {
        this.saveCurrentBlock();
    }, 2000);
}

// Save to server
saveCurrentBlock() {
    if (this.$wire) {
        this.$wire.call('saveBlockDraft', {
            blockId: this.editingBlockId,
            state: this.editingBlockState,
            visible: this.editingBlockVisible
        });
    }
}
```

## Best Practices

### 1. Event Naming

Use consistent event naming patterns:
- `block-editing-started` - When editing begins
- `block-editing-cancelled` - When editing is cancelled
- `block-state-updated` - When block data changes
- `block-visibility-updated` - When visibility changes

### 2. State Synchronization

Always keep client and server state in sync:
- Use events to notify all components of changes
- Update the store immediately for responsive UI
- Save to server asynchronously for persistence

### 3. Error Handling

Handle errors gracefully:
```javascript
try {
    this.$wire.call('saveBlockDraft', data);
} catch (error) {
    console.error('Failed to save block:', error);
    // Show user-friendly error message
}
```

### 4. Performance Optimization

- Use `x-show` instead of `x-if` for frequently toggled content
- Debounce user input to reduce server calls
- Use `wire:key` for dynamic components to prevent re-rendering issues

## Integration with Livewire

### Component Communication

```php
// Livewire component listens for events
#[On('saveBlockDraft')]
public function saveBlockDraft($data): void
{
    // Save data to database
    $this->dispatch('block-draft-saved', ['blockId' => $data['blockId']]);
}

// Alpine.js listens for Livewire events
if (this.$wire) {
    this.$wire.on('block-draft-saved', (event) => {
        // Handle successful save
    });
}
```

### Data Loading

```php
#[On('loadBlockData')]
public function loadBlockData($data): void
{
    $block = ContentBlock::find($data['blockId']);
    $blockState = $block->getTranslatedData($this->activeLocale);
    
    $this->dispatch('block-data-loaded', [
        'blockId' => $data['blockId'],
        'blockState' => $blockState
    ]);
}
```

## Debugging

### Alpine.js DevTools

Enable Alpine.js devtools for debugging:
```javascript
// In your app.js
window.Alpine = Alpine;
Alpine.start();
```

### Event Monitoring

Monitor events in browser console:
```javascript
// Listen for all page builder events
document.addEventListener('block-editing-started', (event) => {
    console.log('Block editing started:', event.detail);
});

document.addEventListener('block-state-updated', (event) => {
    console.log('Block state updated:', event.detail);
});
```

## Migration Guide

### From Server-Side Only

1. **Add Alpine.js data to components**:
   ```blade
   <!-- Before -->
   <div class="block">
   
   <!-- After -->
   <div x-data="blockComponent({{ $block->id }}, {{ json_encode($data) }})">
   ```

2. **Replace server calls with client events**:
   ```blade
   <!-- Before -->
   <button wire:click="editBlock({{ $block->id }})">
   
   <!-- After -->
   <button @click="startEditing()">
   ```

3. **Add reactive data binding**:
   ```blade
   <!-- Before -->
   <div>{{ $data['content'] }}</div>
   
   <!-- After -->
   <div x-html="data.content || ''"></div>
   ```

### From Mixed State Management

1. **Consolidate state in Alpine.js store**
2. **Use events for component communication**
3. **Keep server calls minimal and focused**

This architecture provides a responsive, maintainable page builder with clear separation of concerns between client and server responsibilities. 