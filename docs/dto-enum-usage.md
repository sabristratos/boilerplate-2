# DTO and Enum Usage in Livewire Components

This document outlines best practices for using DTOs and enums in Livewire components to ensure consistent UI display, proper validation, and maintainable code.

## Overview

The codebase uses a combination of:
- **DTOs (Data Transfer Objects)** for type-safe data handling and validation
- **Enums** for consistent status, type, and configuration values
- **Livewire Components** for dynamic user interfaces
- **Services** for business logic and data operations

## Enum Usage in Livewire Components

### Available Enums

The following enums are available with consistent methods:

- `FormStatus` - Form publication status
- `PublishStatus` - General content publication status  
- `ContentBlockStatus` - Content block status
- `MediaType` - Media file types
- `FormElementType` - Form builder element types
- `UserRole` - User role definitions
- `NotificationType` - Notification types
- `SettingGroupKey` - Settings group keys
- `SettingType` - Setting value types

### Enum Methods

Each enum provides these consistent methods:

```php
// Get color for UI display
$enum->getColor(); // Returns: 'blue', 'green', 'amber', etc.

// Get icon for UI display  
$enum->getIcon(); // Returns: 'photo', 'document-text', etc.

// Get human-readable label
$enum->label(); // Returns: 'Draft', 'Published', etc.

// Get detailed description
$enum->getDescription(); // Returns: 'Content is in draft state...'

// Get all options for select dropdowns
EnumClass::options(); // Returns: ['draft' => 'Draft', 'published' => 'Published']

// Get all enum cases
EnumClass::cases(); // Returns array of enum instances
```

### Using the WithEnumHelpers Trait

The `WithEnumHelpers` trait provides convenient methods for Livewire components:

```php
use App\Traits\WithEnumHelpers;

class MyComponent extends Component
{
    use WithEnumHelpers;
    
    public function someMethod()
    {
        // Get enum instances
        $formStatus = $this->getFormStatus('draft');
        $mediaType = $this->getMediaType('image/jpeg');
        
        // Get options for select dropdowns
        $statusOptions = $this->getFormStatusOptions();
        $mediaTypeOptions = $this->getMediaTypeOptions();
        
        // Get complete badge data
        $badgeData = $this->getFormStatusBadge('draft');
        // Returns: ['color' => 'amber', 'icon' => 'document-text', 'label' => 'Draft', 'description' => '...']
    }
}
```

### Example: Forms Index Component

```php
// app/Livewire/Admin/Forms/Index.php
use App\Traits\WithEnumHelpers;

class Index extends Component
{
    use WithPagination, WithEnumHelpers;
    
    public function getFormStatusForForm(Form $form): FormStatus
    {
        $status = $form->status ?? 'draft';
        return $this->getFormStatus($status);
    }
    
    public function getAvailableStatuses(): array
    {
        return $this->getFormStatusOptions();
    }
}
```

```blade
{{-- resources/views/livewire/admin/forms/index.blade.php --}}
<td class="px-6 py-4 whitespace-nowrap">
    @php
        $formStatus = $this->getFormStatusForForm($form);
    @endphp
    <flux:badge :color="$formStatus->getColor()" size="sm" :icon="$formStatus->getIcon()">
        {{ $formStatus->label() }}
    </flux:badge>
</td>
```

### Example: Media Library Component

```php
// app/Livewire/MediaLibrary.php
use App\Traits\WithEnumHelpers;

class MediaLibrary extends Component
{
    use WithPagination, WithEnumHelpers;
    
    public function getMediaTypeForMime(string $mimeType): MediaType
    {
        return $this->getMediaType($mimeType);
    }
    
    public function getIconForMimeType(string $mimeType): string
    {
        $mediaType = $this->getMediaType($mimeType);
        return $mediaType->getIcon();
    }
}
```

```blade
{{-- resources/views/livewire/media-library.blade.php --}}
@else
    @php
        $mediaType = $this->getMediaTypeForMime($item->mime_type);
    @endphp
    <div class="flex h-full w-full items-center justify-center">
        <flux:icon name="{{ $mediaType->getIcon() }}" class="h-16 w-16 text-{{ $mediaType->getColor() }}-400" />
    </div>
@endif
```

## DTO Usage in Livewire Components

### DTO Validation

DTOs provide built-in validation using the `DTOValidationService`:

```php
// In a Livewire component
use App\DTOs\FormDTO;
use App\Services\DTOValidationService;

class MyComponent extends Component
{
    public function createForm(array $data)
    {
        $formDto = FormDTO::fromArray($data);
        
        // Validate using the service
        $validationService = app(DTOValidationService::class);
        $errors = $validationService->validate($formDto);
        
        if (!empty($errors)) {
            foreach ($errors as $field => $message) {
                $this->addError($field, $message);
            }
            return;
        }
        
        // Create form using service
        $formService = app(FormServiceInterface::class);
        $form = $formService->createForm($formDto);
    }
}
```

### DTO Factory Usage

Use the `DTOFactory` to create DTOs from models:

```php
// In a Livewire component
use App\DTOs\DTOFactory;

class MyComponent extends Component
{
    public function getFormData(int $formId): ?FormDTO
    {
        $form = Form::find($formId);
        if (!$form) {
            return null;
        }
        
        return DTOFactory::createFormDTO($form);
    }
}
```

### Example: Form Builder Component

The FormBuilder component demonstrates excellent enum usage:

```php
// app/Livewire/FormBuilder.php
#[Computed]
public function elementTypes(): array
{
    return FormElementType::cases();
}
```

```blade
{{-- resources/views/components/form-builder/toolbox.blade.php --}}
@foreach($elementTypes as $elementType)
<flux:tooltip content="{{ $elementType->getDescription() }}">
    <div class="element-card">
        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
            <flux:icon name="{{ $elementType->getIcon() }}" class="size-4 text-primary-600" />
        </div>
        <flux:heading size="xs">
            {{ $elementType->getLabel() }}
        </flux:heading>
    </div>
</flux:tooltip>
@endforeach
```

## Service Layer Integration

### Using DTOs in Services

Services should use DTOs for type-safe operations:

```php
// app/Services/FormService.php
class FormService implements FormServiceInterface
{
    public function createForm(FormDTO $formDto): Form
    {
        if (!$formDto->isValid()) {
            throw new InvalidArgumentException('Invalid form data: ' . $formDto->getValidationErrorsAsString());
        }
        
        // Create form from DTO
        $form = new Form();
        $form->user_id = $formDto->userId;
        $form->name = $formDto->name;
        $form->elements = $formDto->elements;
        $form->settings = $formDto->settings;
        $form->save();
        
        return $form;
    }
    
    public function getFormById(int $id): ?FormDTO
    {
        $form = Form::find($id);
        if (!$form) {
            return null;
        }
        
        return DTOFactory::createFormDTO($form);
    }
}
```

### Using Enums in Services

Services should use enums for status and type handling:

```php
// In a service
use App\Enums\FormStatus;

class FormService
{
    public function publishForm(Form $form): void
    {
        $form->status = FormStatus::PUBLISHED->value;
        $form->save();
    }
    
    public function getFormsByStatus(FormStatus $status): Collection
    {
        return Form::where('status', $status->value)->get();
    }
}
```

## Best Practices

### 1. Always Use Enum Methods for UI Display

❌ **Don't do this:**
```php
// Hardcoded values
$color = 'green';
$icon = 'check';
$label = 'Published';
```

✅ **Do this:**
```php
// Use enum methods
$status = FormStatus::PUBLISHED;
$color = $status->getColor();
$icon = $status->getIcon();
$label = $status->label();
```

### 2. Use DTOs for Data Validation

❌ **Don't do this:**
```php
// Direct array validation
$this->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
]);
```

✅ **Do this:**
```php
// Use DTO validation
$formDto = FormDTO::fromArray($this->formData);
$errors = app(DTOValidationService::class)->validate($formDto);
```

### 3. Use the WithEnumHelpers Trait

❌ **Don't do this:**
```php
// Direct enum instantiation
$status = FormStatus::from($form->status);
```

✅ **Do this:**
```php
// Use trait helper
use App\Traits\WithEnumHelpers;

class MyComponent extends Component
{
    use WithEnumHelpers;
    
    public function getStatus(Form $form): FormStatus
    {
        return $this->getFormStatus($form->status);
    }
}
```

### 4. Consistent Badge Display

❌ **Don't do this:**
```blade
<flux:badge color="green" size="sm">
    {{ $form->status ?? 'Draft' }}
</flux:badge>
```

✅ **Do this:**
```blade
@php
    $formStatus = $this->getFormStatusForForm($form);
@endphp
<flux:badge :color="$formStatus->getColor()" :icon="$formStatus->getIcon()">
    {{ $formStatus->label() }}
</flux:badge>
```

## Testing

### Testing Enum Usage

```php
// tests/Unit/Livewire/EnumUsageTest.php
class EnumUsageTest extends TestCase
{
    use WithEnumHelpers;
    
    public function test_component_uses_enum_properly(): void
    {
        $component = new MyComponent();
        
        $status = $component->getFormStatus('draft');
        $this->assertInstanceOf(FormStatus::class, $status);
        $this->assertEquals('amber', $status->getColor());
        $this->assertEquals('document-text', $status->getIcon());
    }
}
```

### Testing DTO Validation

```php
// tests/Unit/DTOs/FormDTOIntegrationTest.php
class FormDTOIntegrationTest extends TestCase
{
    public function test_dto_validation_works(): void
    {
        $formData = [
            'name' => ['en' => 'Test Form'],
            'userId' => 1,
        ];
        
        $formDto = FormDTO::fromArray($formData);
        $validationService = app(DTOValidationService::class);
        $errors = $validationService->validate($formDto);
        
        $this->assertEmpty($errors);
    }
}
```

## Migration Guide

### Updating Existing Components

1. **Add the trait:**
```php
use App\Traits\WithEnumHelpers;

class MyComponent extends Component
{
    use WithEnumHelpers;
}
```

2. **Replace hardcoded values:**
```php
// Before
$color = 'green';
$icon = 'check';

// After
$status = $this->getFormStatus($form->status);
$color = $status->getColor();
$icon = $status->getIcon();
```

3. **Update Blade templates:**
```blade
{{-- Before --}}
<flux:badge color="green">{{ $form->status }}</flux:badge>

{{-- After --}}
@php
    $formStatus = $this->getFormStatusForForm($form);
@endphp
<flux:badge :color="$formStatus->getColor()" :icon="$formStatus->getIcon()">
    {{ $formStatus->label() }}
</flux:badge>
```

## Conclusion

By following these patterns, you ensure:

- **Consistency**: All UI elements use the same colors, icons, and labels
- **Maintainability**: Changes to enum values automatically propagate
- **Type Safety**: DTOs provide compile-time validation
- **Testability**: Clear separation of concerns makes testing easier
- **Documentation**: Enum methods serve as living documentation

The combination of DTOs for data handling and enums for UI consistency creates a robust, maintainable, and user-friendly application. 