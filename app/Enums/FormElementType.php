<?php

namespace App\Enums;

enum FormElementType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case EMAIL = 'email';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case DATE = 'date';
    case NUMBER = 'number';
    case PASSWORD = 'password';
    case FILE = 'file';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => 'Text Input',
            self::TEXTAREA => 'Text Area',
            self::EMAIL => 'Email',
            self::SELECT => 'Select',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio',
            self::DATE => 'Date Picker',
            self::NUMBER => 'Number Input',
            self::PASSWORD => 'Password',
            self::FILE => 'File Upload',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::TEXT => 'chat-bubble-bottom-center-text',
            self::TEXTAREA => 'bars-3-bottom-left',
            self::EMAIL => 'at-symbol',
            self::SELECT => 'chevron-up-down',
            self::CHECKBOX => 'check',
            self::RADIO => 'check-circle',
            self::DATE => 'calendar',
            self::NUMBER => 'calculator',
            self::PASSWORD => 'lock-closed',
            self::FILE => 'document-arrow-up',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::TEXT => 'Single line text input for short responses',
            self::TEXTAREA => 'Multi-line text area for longer responses',
            self::EMAIL => 'Email input with built-in email validation',
            self::SELECT => 'Dropdown select with predefined options',
            self::CHECKBOX => 'Checkbox for yes/no or multiple choice selections',
            self::RADIO => 'Radio button for single choice from multiple options',
            self::DATE => 'Date picker with calendar overlay for date selection',
            self::NUMBER => 'Numeric input for numbers with optional min/max values',
            self::PASSWORD => 'Secure password input with visibility toggle',
            self::FILE => 'File upload input for documents, images, and other files',
        };
    }
}
