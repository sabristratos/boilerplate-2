# Data Transfer Objects (DTOs)

This directory contains comprehensive Data Transfer Objects (DTOs) for the application. DTOs provide type-safe, structured data transfer between different layers of the application, ensuring data integrity and consistency.

## Overview

DTOs encapsulate data structures and provide:
- **Type Safety**: Strict typing for all properties
- **Validation**: Built-in validation methods
- **Transformation**: Methods to convert between different data formats
- **Immutability**: Read-only properties prevent accidental modifications
- **Serialization**: JSON and array conversion capabilities

## Available DTOs

### 1. ContentBlockDTO
Handles content block data including type, data, settings, visibility, and ordering.

**Key Features:**
- Translatable data support
- Block visibility management
- Settings and data access methods
- Order management

**Usage:**
```php
// From model
$block = ContentBlock::find(1);
$dto = ContentBlockDTO::fromModel($block);

// From array
$dto = ContentBlockDTO::fromArray($data);

// For creation
$dto = ContentBlockDTO::forCreation('hero', 1, ['title' => 'Welcome']);

// Access data
$title = $dto->getData('title');
$isVisible = $dto->isVisible();
$settings = $dto->getSetting('background_color');
```

### 2. PageDTO
Manages page data including title, slug, status, and SEO metadata.

**Key Features:**
- Translatable content support
- SEO metadata management
- Publish status handling
- Slug validation

**Usage:**
```php
// From model
$page = Page::find(1);
$dto = PageDTO::fromModel($page);

// For creation
$dto = PageDTO::forCreation(
    ['en' => 'Home', 'fr' => 'Accueil'],
    'home',
    PublishStatus::Draft
);

// Access data
$title = $dto->getTitleForLocale('en');
$isPublished = $dto->isPublished();
$metaTitle = $dto->getMetaTitleForLocale('en');
```

### 3. FormSubmissionDTO
Handles form submission data with metadata and validation.

**Key Features:**
- Form data access and manipulation
- IP address and user agent tracking
- Sensitive data detection and sanitization
- Submission age calculation

**Usage:**
```php
// From model
$submission = FormSubmission::find(1);
$dto = FormSubmissionDTO::fromModel($submission);

// For creation
$dto = FormSubmissionDTO::forCreation(
    1,
    ['name' => 'John', 'email' => 'john@example.com'],
    '192.168.1.1',
    'Mozilla/5.0...'
);

// Access data
$name = $dto->getFieldValue('name');
$hasSensitiveData = $dto->containsSensitiveData();
$formattedData = $dto->getFormattedData();
```

### 4. UserDTO
Manages user data including authentication and profile information.

**Key Features:**
- Social login support (Google, Facebook)
- Role and permission management
- Email verification status
- Profile data access

**Usage:**
```php
// From model
$user = User::find(1);
$dto = UserDTO::fromModel($user);

// For creation
$dto = UserDTO::forCreation('John Doe', 'john@example.com', 'password123');

// Access data
$initials = $dto->getInitials();
$hasSocialLogin = $dto->hasSocialLogin();
$isAdmin = $dto->isAdmin();
$hasRole = $dto->hasRole('editor');
```

### 5. MediaDTO
Handles media file data including metadata and conversions.

**Key Features:**
- File type detection (image, video, audio, document)
- Media conversions management
- File size formatting
- Custom properties access

**Usage:**
```php
// From model
$media = Media::find(1);
$dto = MediaDTO::fromModel($media);

// For creation
$dto = MediaDTO::forCreation(
    'image.jpg',
    'Hero Image',
    'image/jpeg',
    1024000,
    'public',
    'images/hero.jpg',
    'hero',
    'App\Models\Page',
    1
);

// Access data
$isImage = $dto->isImage();
$formattedSize = $dto->getFormattedSize();
$conversionUrl = $dto->getConversionUrl('thumbnail');
$extension = $dto->getExtension();
```

## Base DTO Class

All DTOs extend the `BaseDTO` abstract class which provides:

- **JSON Serialization**: `toJson()` and `jsonSerialize()` methods
- **Array Conversion**: `toArray()` method
- **Validation**: `validate()` and `isValid()` methods
- **Error Handling**: `getValidationErrorsAsString()` method
- **Immutability**: `with()` method for creating modified copies

## DTO Factory

The `DTOFactory` class provides a centralized way to create DTOs:

```php
// Create single DTOs
$blockDto = DTOFactory::createContentBlockDTO($block);
$pageDto = DTOFactory::createPageDTO($page);

// Create multiple DTOs from collections
$blockDtos = DTOFactory::createContentBlockDTOs($blocks);
$pageDtos = DTOFactory::createPageDTOs($pages);

// Create DTOs for new entities
$newBlockDto = DTOFactory::createContentBlockDTOForCreation('hero', 1);
$newPageDto = DTOFactory::createPageDTOForCreation(['en' => 'New Page'], 'new-page');
```

## Validation

All DTOs include built-in validation:

```php
$dto = ContentBlockDTO::forCreation('', 0); // Invalid data

if (!$dto->isValid()) {
    $errors = $dto->validate();
    $errorString = $dto->getValidationErrorsAsString();
    // Handle validation errors
}
```

## Best Practices

### 1. Use DTOs for Data Transfer
```php
// ✅ Good: Use DTOs for service layer communication
public function createPage(PageDTO $pageDto): Page
{
    // Service logic here
}

// ❌ Bad: Pass raw arrays or models directly
public function createPage(array $data): Page
{
    // Less type-safe and harder to validate
}
```

### 2. Validate DTOs Before Use
```php
// ✅ Good: Always validate DTOs
$dto = PageDTO::fromArray($data);
if (!$dto->isValid()) {
    throw new InvalidArgumentException($dto->getValidationErrorsAsString());
}

// ❌ Bad: Assume data is valid
$dto = PageDTO::fromArray($data);
// Use without validation
```

### 3. Use Factory for Complex Creation
```php
// ✅ Good: Use factory for complex DTO creation
$dto = DTOFactory::createPageDTO($page);

// ❌ Bad: Direct instantiation
$dto = new PageDTO(/* many parameters */);
```

### 4. Leverage Immutability
```php
// ✅ Good: Create modified copies
$updatedDto = $dto->with(['title' => 'New Title']);

// ❌ Bad: Try to modify directly (won't work due to readonly properties)
$dto->title = 'New Title'; // This will fail
```

## Integration with Services

DTOs work seamlessly with the service layer:

```php
class PageService
{
    public function createPage(PageDTO $pageDto): Page
    {
        if (!$pageDto->isValid()) {
            throw new InvalidArgumentException('Invalid page data');
        }

        $page = new Page();
        $page->title = $pageDto->title;
        $page->slug = $pageDto->slug;
        $page->status = $pageDto->status;
        // ... set other properties

        $page->save();
        return $page;
    }

    public function updatePage(Page $page, PageDTO $pageDto): Page
    {
        if (!$pageDto->isValid()) {
            throw new InvalidArgumentException('Invalid page data');
        }

        $page->title = $pageDto->title;
        $page->slug = $pageDto->slug;
        $page->status = $pageDto->status;
        // ... update other properties

        $page->save();
        return $page;
    }
}
```

## Testing

DTOs are easily testable:

```php
class ContentBlockDTOTest extends TestCase
{
    public function test_creates_valid_dto_from_array(): void
    {
        $data = [
            'type' => 'hero',
            'page_id' => 1,
            'data' => ['title' => 'Welcome'],
            'settings' => ['background' => 'blue'],
            'visible' => true,
            'order' => 1,
        ];

        $dto = ContentBlockDTO::fromArray($data);

        $this->assertTrue($dto->isValid());
        $this->assertEquals('hero', $dto->type);
        $this->assertEquals('Welcome', $dto->getData('title'));
        $this->assertEquals('blue', $dto->getSetting('background'));
    }

    public function test_validates_required_fields(): void
    {
        $dto = ContentBlockDTO::forCreation('', 0);

        $this->assertFalse($dto->isValid());
        $this->assertArrayHasKey('type', $dto->validate());
        $this->assertArrayHasKey('page_id', $dto->validate());
    }
}
```

## Migration Guide

To migrate existing code to use DTOs:

1. **Replace direct model usage** in service methods with DTOs
2. **Update validation** to use DTO validation methods
3. **Modify data access** to use DTO getter methods
4. **Update tests** to create and validate DTOs
5. **Use factory methods** for consistent DTO creation

## Future Enhancements

- [ ] Add more specialized DTOs (FormDTO, SettingDTO, etc.)
- [ ] Implement DTO transformers for complex data structures
- [ ] Add caching support for frequently used DTOs
- [ ] Create DTO collections for batch operations
- [ ] Add DTO versioning for API compatibility 