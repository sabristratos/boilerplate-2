<?php

namespace App\Services\FormBuilder;

use Illuminate\Support\Str;

class FieldNameGeneratorService
{
    /**
     * Generate a consistent field name from an element
     */
    public function generateFieldName(array $element): string
    {
        $label = $element['properties']['label'] ?? '';
        $id = $element['id'] ?? '';
        
        // Create a more readable field name based on the label
        $fieldName = Str::slug($label, '_');
        
        // Ensure uniqueness by appending ID if needed
        return $fieldName ?: 'field_' . $id;
    }

    /**
     * Generate a simple field name (for backward compatibility)
     * @deprecated Use generateFieldName instead
     */
    public function generateSimpleFieldName(array $element): string
    {
        return $this->generateFieldName($element);
    }
} 