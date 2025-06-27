<?php

namespace App\Services;

use App\Forms\FieldTypeManager;
use App\Models\Form;
use App\Models\FormField;

class FormFieldValidator
{
    protected FieldTypeManager $fieldTypeManager;

    public function __construct(FieldTypeManager $fieldTypeManager)
    {
        $this->fieldTypeManager = $fieldTypeManager;
    }

    public function getRules(Form $form): array
    {
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldType = $this->fieldTypeManager->find($field->type->value);
            if (!$fieldType) {
                continue;
            }

            $fieldRules = $fieldType->getBaseValidationRules($field);

            if (!empty($field->validation_rules)) {
                $fieldRules = array_merge($fieldRules, explode('|', $field->validation_rules));
            }

            if (empty($fieldRules)) {
                continue;
            }

            $rules['formData.' . $field->name] = array_unique($fieldRules);
        }
        return $rules;
    }

    public function getAttributes(Form $form): array
    {
        $attributes = [];
        foreach ($form->fields as $field) {
            $attributes['formData.' . $field->name] = $field->getTranslation('label', app()->getLocale());
        }
        return $attributes;
    }

    public function getRules(FormField $field, array $input): array
    {
        $rules = [];
        $fieldName = "fields.{$field->name}";
        $fieldRules = $field->validation_rules ? explode('|', $field->validation_rules) : [];

        $rules[$fieldName] = $fieldRules;

        return $rules;
    }
} 