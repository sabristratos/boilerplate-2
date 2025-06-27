# Page System Tests

This directory contains tests for the page system, which includes the `Page` model, `ContentBlock` model, and `PageController`.

## Overview

The page system allows for the creation and management of dynamic pages with content blocks. Key features include:

- Pages with translatable titles and slugs
- Content blocks that can be ordered and have different types
- Media attachments for both pages and content blocks
- Support for multiple languages

## Test Files

### PageTest.php

Tests for the `Page` model, including:

- CRUD operations (create, read, update, delete)
- Translation functionality
- Media attachments
- Relationships with content blocks
- Route binding resolution

### ContentBlockTest.php

Tests for the `ContentBlock` model, including:

- CRUD operations
- Translation functionality
- Media attachments
- Default data merging
- Ordering within a page
- Relationships with pages

### PageControllerTest.php

Tests for the `PageController`, including:

- Displaying pages
- Displaying pages with content blocks
- Multilingual page display
- 404 handling for non-existent pages
- Content block ordering
- Only displaying published content blocks

## Running the Tests

To run all page system tests:

```bash
php artisan test --filter="Tests\Feature\Pages"
```

To run a specific test file:

```bash
php artisan test --filter=PageTest
php artisan test --filter=ContentBlockTest
php artisan test --filter=PageControllerTest
```

## Key Components

### Page Model

The `Page` model represents a page in the system. It uses the `HasTranslations` trait for multilingual support and the `InteractsWithMedia` trait for media attachments.

### ContentBlock Model

The `ContentBlock` model represents a content block within a page. It uses the `SortableTrait` for ordering, `HasTranslations` for multilingual support, and `InteractsWithMedia` for media attachments.

### BlockManager Service

The `BlockManager` service manages the available block types and provides methods to find block classes by type.

### Block Classes

Block classes (e.g., `HeroSectionBlock`) define the structure and behavior of different types of content blocks, including:

- Default data
- Validation rules
- View paths for admin and frontend
- Translatable fields

## Testing Considerations

When testing the page system, consider the following:

1. **Translations**: Test with multiple languages to ensure content is correctly translated.
2. **Media**: Test media attachments for both pages and content blocks.
3. **Ordering**: Test that content blocks are displayed in the correct order.
4. **Block Types**: Test with different block types to ensure they are rendered correctly.
5. **Status**: Test that only published content blocks are displayed on the frontend.
