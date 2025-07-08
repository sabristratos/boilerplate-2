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
    case SubmitButton = 'submit_button';

    /**
     * Get the label for the form element type
     */
    public function label(): string
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
            self::SubmitButton => 'Submit Button',
        };
    }

    /**
     * Get the icon for the form element type
     */
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
            self::SubmitButton => 'arrow-right-circle',
        };
    }

    /**
     * Get the description for the form element type
     */
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
            self::SubmitButton => 'Submission button for sending the form',
        };
    }

    /**
     * Get all available form element types as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::TEXT->value => 'Text Input',
            self::TEXTAREA->value => 'Text Area',
            self::EMAIL->value => 'Email',
            self::SELECT->value => 'Select',
            self::CHECKBOX->value => 'Checkbox',
            self::RADIO->value => 'Radio',
            self::DATE->value => 'Date Picker',
            self::NUMBER->value => 'Number Input',
            self::PASSWORD->value => 'Password',
            self::FILE->value => 'File Upload',
            self::SubmitButton->value => 'Submit Button',
        ];
    }

    /**
     * Get the color for the form element type
     */
    public function getColor(): string
    {
        return match ($this) {
            self::TEXT => 'blue',
            self::TEXTAREA => 'cyan',
            self::EMAIL => 'sky',
            self::SELECT => 'indigo',
            self::CHECKBOX => 'green',
            self::RADIO => 'emerald',
            self::DATE => 'amber',
            self::NUMBER => 'teal',
            self::PASSWORD => 'red',
            self::FILE => 'purple',
            self::SubmitButton => 'lime',
        };
    }

    /**
     * Check if the element type is a text input
     */
    public function isTextInput(): bool
    {
        return in_array($this, [self::TEXT, self::TEXTAREA, self::EMAIL, self::PASSWORD]);
    }

    /**
     * Check if the element type is a choice input
     */
    public function isChoiceInput(): bool
    {
        return in_array($this, [self::SELECT, self::CHECKBOX, self::RADIO]);
    }

    /**
     * Check if the element type is a date/time input
     */
    public function isDateTimeInput(): bool
    {
        return $this == self::DATE;
    }

    /**
     * Check if the element type is a numeric input
     */
    public function isNumericInput(): bool
    {
        return $this == self::NUMBER;
    }

    /**
     * Check if the element type is a file input
     */
    public function isFileInput(): bool
    {
        return $this == self::FILE;
    }

    /**
     * Check if the element type is a submit button
     */
    public function isSubmitButton(): bool
    {
        return $this === self::SubmitButton;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
