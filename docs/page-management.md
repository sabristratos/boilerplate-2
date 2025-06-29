# Page Management System Documentation

This document provides a comprehensive overview of the page management system, from creating and managing pages to building custom content blocks.

## 1. Overview

The page management system is a powerful and flexible feature that allows administrators to create, manage, and publish pages with dynamic content. It is built on a block-based architecture, where pages are composed of different types of content blocks. This approach provides a great deal of flexibility for content creators.

The system is primarily managed through the admin area and leverages Livewire for a reactive and modern user experience.

## 2. Managing Pages

The main interface for managing pages is the **Pages** section in the admin panel. This is handled by the `App\Livewire\Admin\PageIndex` Livewire component.

### Features:

*   **List Pages**: Displays a paginated list of all created pages.
*   **Search**: Allows searching for pages by their title or slug.
*   **Sorting**: Pages can be sorted by title.
*   **Filtering**: Pages can be filtered by their translation status for different locales.
*   **Create Page**: A "New Page" button allows for the creation of a new page. When clicked, a new page with a default title and a unique slug is created, and the user is redirected to the page editor.
*   **Delete Page**: Pages can be deleted from the list. A confirmation modal is shown to prevent accidental deletion.

## 3. The Page Editor

The page editor is where the content and details of a specific page are managed. This is handled by the `App\Livewire\Admin\PageManager` component.

### Page Details

In the editor's "Settings" tab, you can manage the page's core details:

*   **Title**: The title of the page. This is a translatable field, allowing for different titles in different languages.
*   **Slug**: The URL-friendly identifier for the page. A "Generate" button is available to automatically create a slug based on the page's title in the default locale.

Changes to these details can be saved using the "Save details" button.

### Localization

The editor fully supports localization:

*   A dropdown menu allows switching between available locales.
*   When a locale is selected, the page reloads to show the content for that specific language.
*   The `title` field can be filled in for each locale.

### Content Blocks

The core of the page editor is the block-based content area. This is where you build the content of your page.

*   **Add Blocks**: A variety of predefined block types can be added to the page.
*   **Reorder Blocks**: Blocks can be reordered by dragging and dropping them into the desired position.
*   **Edit Blocks**: Each block can be edited by clicking the "edit" icon. This opens the block's form in the sidebar.
*   **Delete Blocks**: Blocks can be deleted, with a confirmation step to prevent accidents.

## 4. Content Blocks

Content blocks are the fundamental building blocks of pages. Each block is a self-contained piece of content with its own fields, validation, and rendering logic.

### Block Editor

When a block is edited, its form is loaded into the sidebar. This is managed by the `App\Livewire\Admin\BlockEditor` component.

*   **Editing Form**: The editor displays the form fields specific to the block being edited.
*   **Autosaving**: To prevent data loss, changes are automatically saved periodically. A manual "Save" button is also available.
*   **Status**: Each block has a status (e.g., Draft, Published) that can be changed in the editor.
*   **Translatable Fields**: If a block has translatable fields, their content is managed based on the currently active locale in the page editor.

## 5. Creating a New Block Type

The system is designed to be extensible. You can easily create your own custom block types.

### Steps to Create a Block:

1.  **Create a Block Class**:
    Create a new PHP class in the `app/Blocks` directory that extends `App\Blocks\Block`. For example, `app/Blocks/MyCustomBlock.php`.

    ```php
    <?php

    namespace App\Blocks;

    class MyCustomBlock extends Block
    {
        public function getName(): string
        {
            return 'My Custom Block';
        }

        public function getIcon(): string
        {
            return 'cube'; // Heroicon name
        }

        public function getDefaultData(): array
        {
            return [
                'custom_heading' => 'Default Heading',
            ];
        }

        public function getTranslatableFields(): array
        {
            return ['custom_heading'];
        }

        public function validationRules(): array
        {
            return [
                'custom_heading' => 'required|string|max:255',
            ];
        }
    }
    ```

2.  **Create the Admin View (Editor Form)**:
    Create a Blade file in `resources/views/livewire/admin/block-forms/`. The filename should correspond to the block's type (e.g., `_my-custom.blade.php`). This view will contain the form fields for editing the block.

    ```html
    <!-- resources/views/livewire/admin/block-forms/_my-custom.blade.php -->
    <x-flux::input
        wire:model.live="state.custom_heading"
        label="Heading"
        :translatable="true"
        :active-locale="$activeLocale"
    />
    ```

3.  **Create the Frontend View**:
    Create a Blade file in `resources/views/frontend/blocks/` (e.g., `_my-custom.blade.php`). This view will render the block's content on the live site.

    ```html
    <!-- resources/views/frontend/blocks/_my-custom.blade.php -->
    <section>
        <h2>{{ $block->getTranslated('custom_heading', $data) }}</h2>
    </section>
    ```

The `BlockManager` service will automatically discover your new block, making it available in the page editor.

## 6. Frontend Rendering

When a visitor accesses a page's URL, the `App\Http\Controllers\PageController` handles the request.

*   The controller fetches the `Page` model from the database using route-model binding.
*   It passes the `Page` object to the `resources/views/pages/show.blade.php` view.
*   The view then iterates through the page's `contentBlocks`. For each block that has a "published" status, it:
    1.  Uses the `BlockManager` to find the corresponding block class.
    2.  Includes the block's frontend view partial, passing in the block's data.

This process ensures that only published blocks are rendered and that each block is displayed using its own specific template. 