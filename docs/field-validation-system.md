# Field-Specific Validation System

## Overview

The form builder now includes a smart validation system that only shows relevant validation rules for each field type. This makes the interface cleaner and more user-friendly while ensuring that users can only apply appropriate validations to each field.

## Architecture

### Core Components

1. **FieldValidationService** (`app/Services/FormBuilder/FieldValidationService.php`)
   - Maps field types to relevant validation rules
   - Groups rules by category for better organization
   - Provides a scalable and maintainable approach

2. **ValidationService** (`app/Services/FormBuilder/ValidationService.php`)
   - Updated to use FieldValidationService
   - Maintains backward compatibility
   - Handles rule generation and message creation

3. **FormBuilder Livewire Component** (`app/Livewire/FormBuilder.php`)
   - Updated `availableValidationRules` computed property
   - Returns field-specific rules based on selected element

## Field Type Validation Rules

### Text Input
- **Basic**: Required
- **Length**: Minimum Length, Maximum Length
- **Format**: Letters Only, Letters & Numbers, Letters Numbers & Dashes, Valid URL
- **Advanced**: Custom Pattern (Regex)

### Textarea
- **Basic**: Required
- **Length**: Minimum Length, Maximum Length

### Email
- **Basic**: Required
- **Format**: Valid Email
- **Length**: Maximum Length

### Select
- **Basic**: Required

### Checkbox
- **Basic**: Required

### Radio
- **Basic**: Required

### Date
- **Basic**: Required
- **Format**: Valid Date
- **Date Range**: Date After, Date Before

### Number
- **Basic**: Required
- **Format**: Numeric, Integer
- **Range**: Minimum Value, Maximum Value, Positive Number, Negative Number

### Password
- **Basic**: Required
- **Length**: Minimum Length, Maximum Length
- **Security**: Password Confirmation

### File
- **Basic**: Required
- **Format**: Valid File, Image File, File Type
- **Size**: Maximum File Size

## Categories

Validation rules are organized into logical categories:

- **Basic**: Fundamental validation rules (required, etc.)
- **Length**: Character/string length restrictions
- **Format**: Data format validation
- **Range**: Numeric range validation
- **Date Range**: Date-specific range validation
- **Security**: Security-related validations
- **Size**: File size restrictions
- **Advanced**: Complex validation patterns

## Adding New Validation Rules

### 1. Add to FieldValidationService

```php
private function getTextRules(): array
{
    return [
        // ... existing rules
        'new_rule' => [
            'label' => 'New Rule',
            'description' => 'Description of the new rule',
            'rule' => 'laravel_validation_rule',
            'icon' => 'icon-name',
            'has_value' => true, // or false
            'category' => 'Category Name',
        ],
    ];
}
```

### 2. Update Configuration (if needed)

Add default messages to `config/forms.php`:

```php
'default_messages' => [
    // ... existing messages
    'new_rule' => 'The :field field is invalid.',
],
```

### 3. Add to Available Rules (if needed)

Add to `config/forms.php` validation rules section if the rule requires special handling.

## Benefits

1. **User Experience**: Only relevant rules are shown, reducing confusion
2. **Maintainability**: Easy to add new field types and validation rules
3. **Scalability**: Clean separation of concerns
4. **Consistency**: Organized by categories for better understanding
5. **Performance**: No unnecessary validation options loaded

## Usage Example

```php
// Get relevant rules for a text field
$fieldValidationService = new FieldValidationService();
$textRules = $fieldValidationService->getRelevantRules('text');

// Get rules grouped by category
$groupedRules = $fieldValidationService->getRelevantRulesByCategory('text');

// Get available categories for a field type
$categories = $fieldValidationService->getAvailableCategories('text');
```

## Future Enhancements

- Custom validation rule builder
- Validation rule presets for common use cases
- Conditional validation rules
- Validation rule dependencies
- Advanced regex pattern builder 