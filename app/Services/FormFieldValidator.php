<?php

namespace App\Services;

use App\Models\Form;
use App\Enums\FormFieldType;

class FormFieldValidator
{
    public function getRules(Form $form): array
    {
        $rules = [];
        foreach ($form->formFields as $field) {
            $fieldRules = [];
            if ($field->is_required) {
                $fieldRules[] = 'required';
            }

            switch ($field->type) {
                case FormFieldType::EMAIL:
                    $fieldRules[] = 'email';
                    break;
                case FormFieldType::NUMBER:
                    $fieldRules[] = 'numeric';
                    break;
                case FormFieldType::DATE:
                    $fieldRules[] = 'date';
                    break;
                case FormFieldType::FILE:
                    $fieldRules[] = 'file';
                    break;
            }

            if (!empty($field->validation_rules)) {
                $fieldRules[] = $field->validation_rules;
            }

            if (empty($fieldRules)) {
                continue;
            }

            $rules['formData.' . $field->name] = implode('|', $fieldRules);
        }
        return $rules;
    }

    public function getAttributes(Form $form): array
    {
        $attributes = [];
        foreach ($form->formFields as $field) {
            if (empty($field->validation_rules)) {
                continue;
            }
            $attributes['formData.' . $field->name] = $field->getTranslation('label', app()->getLocale());
        }
        return $attributes;
    }
} 