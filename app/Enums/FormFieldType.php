<?php

namespace App\Enums;

enum FormFieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case DATE = 'date';
    case TIME = 'time';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case FILE = 'file';
    case SECTION = 'section';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => 'Text Input',
            self::TEXTAREA => 'Text Area',
            self::SELECT => 'Select Menu',
            self::EMAIL => 'Email',
            self::NUMBER => 'Number',
            self::DATE => 'Date',
            self::TIME => 'Time',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio Buttons',
            self::FILE => 'File Upload',
            self::SECTION => 'Section Break',
        };
    }
} 