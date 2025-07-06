# PageManager Refactoring Documentation

## Overview

The PageManager system has been refactored to follow Livewire best practices and improve maintainability. The original monolithic component has been broken down into smaller, focused components that each handle a specific concern.

## Architecture Changes

### Before (Monolithic Approach)
- **Single Component**: `PageManager` (633 lines)
- **Responsibilities**: Page management, block editing, block library, UI state, Alpine.js integration
- **Issues**: 
  - Single Responsibility Principle violation
  - Complex state management
  - Tight coupling between concerns
  - Performance issues with large component
  - Difficult to maintain and test

### After (Component-Based Approach)
- **Main Component**: `PageManager` (simplified to ~200 lines)
- **Child Components**:
  - `PageCanvas`: Handles block display and editing
  - `BlockLibrary`: Manages block creation and filtering
  - `BlockEditor`: Handles block form editing

## Component Breakdown

### 1. PageManager (Parent Component)
**Responsibilities:**
- Page-level properties (title, slug, meta data)
- Locale management
- Page saving
- Event coordination between child components

**Key Features:**
- Uses `#[On]` attributes to listen for events from child components
- Delegates specific functionality to child components
- Maintains only page-level state

### 2. PageCanvas Component
**Responsibilities:**
- Displaying content blocks
- Block reordering via drag-and-drop
- Block editing initiation
- Block deletion

**Key Features:**
- Uses `#[Reactive]` for `$activeLocale` prop
- Handles Alpine.js integration for real-time preview
- Dispatches events to communicate with other components

### 3. BlockLibrary Component
**Responsibilities:**
- Block search and filtering
- Block creation
- Available blocks display

**Key Features:**
- Independent filtering logic
- Clean separation of block creation concerns
- Dispatches events when blocks are created

### 4. BlockEditor Component
**Responsibilities:**
- Block form editing
- Block state management
- Auto-saving functionality

**Key Features:**
- Uses `#[Reactive]` for `$activeLocale` and `$editingBlockId` props
- Handles block data loading and saving
- Communicates via events with other components

## Communication Patterns

### Parent-Child Communication
1. **Props**: Data flows down via props
2. **Events**: Actions flow up via events using `$dispatch()`
3. **Reactive Props**: Use `#[Reactive]` for real-time updates

### Event Flow
```
PageCanvas → $dispatch('edit-block') → BlockEditor
BlockEditor → $dispatch('block-state-updated') → PageCanvas (Alpine.js)
BlockLibrary → $dispatch('block-created') → PageManager
PageCanvas → $dispatch('block-deleted') → PageManager
```

## Benefits of Refactoring

### 1. **Improved Maintainability**
- Each component has a single, clear responsibility
- Easier to locate and fix bugs
- Simpler to add new features

### 2. **Better Performance**
- Smaller components load faster
- Reduced memory usage
- More efficient re-rendering

### 3. **Enhanced Testability**
- Components can be tested in isolation
- Easier to mock dependencies
- Clearer test boundaries

### 4. **Code Reusability**
- Components can be reused in other contexts
- BlockLibrary could be used in other editors
- BlockEditor could be used for standalone block editing

### 5. **Developer Experience**
- Easier to understand and navigate
- Reduced cognitive load
- Better separation of concerns

## Livewire Best Practices Applied

### 1. **Component Nesting**
- Used nested Livewire components for logical separation
- Each component has a unique `:key` for proper tracking

### 2. **Reactive Props**
- Used `#[Reactive]` for props that need real-time updates
- Eliminates manual event handling for simple data flow

### 3. **Event Communication**
- Prefer `$dispatch()` over server roundtrips
- Use `#[On]` attributes for event handling
- Clear event naming conventions

### 4. **Computed Properties**
- Moved complex logic to computed properties
- Improved performance and readability

### 5. **Lifecycle Hooks**
- Proper use of `boot()`, `mount()`, `updated()`, `dehydrate()`
- Clean state management

## Migration Guide

### For Developers
1. **New Components**: Use the new child components for specific functionality
2. **Event Handling**: Use `$dispatch()` and `#[On]` for component communication
3. **Props**: Use `#[Reactive]` for real-time data binding
4. **Testing**: Test components in isolation

### For Block Developers
- No changes required to block implementations
- Block forms continue to work as before
- Block views remain unchanged

## Future Improvements

### 1. **Further Componentization**
- Extract `PageSettings` component for page-level settings
- Create `BlockPreview` component for preview functionality

### 2. **State Management**
- Consider using Livewire's state management features
- Implement proper error boundaries

### 3. **Performance Optimizations**
- Implement lazy loading for block forms
- Add caching for block library data

### 4. **Testing**
- Add comprehensive unit tests for each component
- Implement integration tests for component communication

## Conclusion

The refactored PageManager system is now more maintainable, performant, and follows Livewire best practices. The component-based architecture provides better separation of concerns and makes the codebase easier to understand and extend.

The refactoring reduces the main component from 633 lines to approximately 200 lines while maintaining all existing functionality. Each child component has a clear, single responsibility and communicates effectively with other components through well-defined interfaces. 