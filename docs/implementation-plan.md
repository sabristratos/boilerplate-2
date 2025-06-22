# Block-Based Content System Implementation Plan

This document outlines the steps required to implement the flexible, block-based content management system. It is based on the architectural blueprint provided, with a focus on deep integration with the Flux UI component library.

### 1. Data Layer & Backend (Laravel)

- [x] Create `Page` and `ContentBlock` Eloquent models.
- [x] Create database migrations for `pages` and `content_blocks` tables.
    - [x] `pages` table should have a JSON `title` and `slug` for translations.
    - [x] `content_blocks` table should include `page_id`, `type` (string), `data` (json for translatable content), and `order` (integer).
- [x] Implement `spatie/laravel-translatable` on the `Page` model for the `title` and `slug` attributes.
- [x] Implement `spatie/eloquent-sortable` on the `ContentBlock` model to handle reordering within a page.
    - [x] `ordered` scope will be available via the package.
    - [x] `setNewOrder` method will be available via the package.
- [x] Implement `spatie/laravel-medialibrary` on both `Page` (for a featured image, for example) and `ContentBlock` models.
- [x] Create an `UpdateContentBlockAction` class in `app/Actions/Content/` that uses the media library to handle image uploads and correctly saves translatable data within the `data` JSON attribute.
- [x] Create a `ContentBlockPolicy` to manage user permissions for updating blocks.
- [x] Add a new `edit pages` permission to the `RolesAndPermissionsSeeder` and assign it to the appropriate roles.
- [x] Create new routes to handle page management:
    - [x] An index route `/admin/pages` to list all pages.
    - [x] An editor route `/admin/pages/{page}/editor`.
- [x] Add a "Pages" link to the main sidebar navigation.

### 2. Real-Time Component Architecture (Livewire & Flux UI)

- [x] Create a `PageIndex` Livewire component to list and manage pages.
- [x] Add the `<flux:toast />` component to the main application layout.
- [x] Create the `PageManager` Livewire component at `app/Livewire/Admin/PageManager.php` as defined in the blueprint.
- [x] Create the corresponding Blade view at `resources/views/livewire/admin/page-manager.blade.php`.
- [x] Integrate a drag-and-drop library (e.g., Livewire's built-in `wire:sortable` functionality) to enable reordering of content blocks.
- [x] Create a new directory for block-specific form partials: `resources/views/livewire/admin/block-forms/`.
- [x] For each content block type, create a corresponding form partial (e.g., `_hero-section.blade.php`, `_text-and-image.blade.php`) using Flux UI components (`flux:input`, `flux:textarea`, etc.).

### 3. Testing Strategy

- [ ] Write backend (Pest/PHPUnit) tests for the `UpdateContentBlockAction` to validate the business logic.
- [ ] Write Livewire component tests for `PageManager.php`.
    - [ ] Test that the edit modal is correctly populated and displayed by mocking the `Flux` facade (`Flux::shouldReceive('modal->show')`).
    - [ ] Test that the `saveBlock` method validates input, calls the action, and triggers the correct Flux UI events (modal close and toast).
    - [ ] Test that the `updateBlockOrder` method correctly calls `ContentBlock::setNewOrder`.
- [ ] (Optional but Recommended) Write End-to-End (Dusk) tests to simulate a user creating, editing, and reordering blocks on a page.

### 4. Scalability & Best Practices

- [ ] Review the final implementation for potential performance optimizations, such as lazy loading blocks if a page can contain a large number of them.
- [ ] Consider implementing optimistic locking on `ContentBlock` models if there's a high chance of multiple admins editing the same page concurrently.
- [ ] Ensure the development team is familiar with the idiomatic way of interacting with Flux components from Alpine.js, primarily using the `$flux` magic helper instead of manual state management.