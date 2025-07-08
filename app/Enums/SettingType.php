<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case SELECT = 'select';
    case FILE = 'file';
    case MEDIA = 'media';
    case COLOR = 'color';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case PASSWORD = 'password';
    case RANGE = 'range';
    case TEL = 'tel';
    case TIME = 'time';
    case URL = 'url';
    case REPEATER = 'repeater';

    /**
     * Get all available setting types as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::TEXT->value => 'Text Input',
            self::TEXTAREA->value => 'Text Area',
            self::CHECKBOX->value => 'Checkbox',
            self::RADIO->value => 'Radio Button',
            self::SELECT->value => 'Select Dropdown',
            self::FILE->value => 'File Upload',
            self::MEDIA->value => 'Media Picker',
            self::COLOR->value => 'Color Picker',
            self::DATE->value => 'Date Picker',
            self::DATETIME->value => 'Date & Time Picker',
            self::EMAIL->value => 'Email Input',
            self::NUMBER->value => 'Number Input',
            self::PASSWORD->value => 'Password Input',
            self::RANGE->value => 'Range Slider',
            self::TEL->value => 'Telephone Input',
            self::TIME->value => 'Time Picker',
            self::URL->value => 'URL Input',
            self::REPEATER->value => 'Repeater Field',
        ];
    }

    /**
     * Get the label for the setting type
     */
    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text Input',
            self::TEXTAREA => 'Text Area',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio Button',
            self::SELECT => 'Select Dropdown',
            self::FILE => 'File Upload',
            self::MEDIA => 'Media Picker',
            self::COLOR => 'Color Picker',
            self::DATE => 'Date Picker',
            self::DATETIME => 'Date & Time Picker',
            self::EMAIL => 'Email Input',
            self::NUMBER => 'Number Input',
            self::PASSWORD => 'Password Input',
            self::RANGE => 'Range Slider',
            self::TEL => 'Telephone Input',
            self::TIME => 'Time Picker',
            self::URL => 'URL Input',
            self::REPEATER => 'Repeater Field',
        };
    }

    /**
     * Get the color for the setting type
     */
    public function getColor(): string
    {
        return match ($this) {
            self::TEXT => 'blue',
            self::TEXTAREA => 'cyan',
            self::CHECKBOX => 'green',
            self::RADIO => 'emerald',
            self::SELECT => 'indigo',
            self::FILE => 'purple',
            self::MEDIA => 'pink',
            self::COLOR => 'rose',
            self::DATE => 'amber',
            self::DATETIME => 'orange',
            self::EMAIL => 'sky',
            self::NUMBER => 'teal',
            self::PASSWORD => 'red',
            self::RANGE => 'violet',
            self::TEL => 'fuchsia',
            self::TIME => 'yellow',
            self::URL => 'lime',
            self::REPEATER => 'zinc',
        };
    }

    /**
     * Get the icon for the setting type
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::TEXT => 'chat-bubble-bottom-center-text',
            self::TEXTAREA => 'bars-3-bottom-left',
            self::CHECKBOX => 'check-square',
            self::RADIO => 'check-circle',
            self::SELECT => 'chevron-up-down',
            self::FILE => 'document-arrow-up',
            self::MEDIA => 'photo',
            self::COLOR => 'swatch',
            self::DATE => 'calendar',
            self::DATETIME => 'clock',
            self::EMAIL => 'envelope',
            self::NUMBER => 'calculator',
            self::PASSWORD => 'lock-closed',
            self::RANGE => 'arrows-right-left',
            self::TEL => 'phone',
            self::TIME => 'clock',
            self::URL => 'link',
            self::REPEATER => 'list-bullet',
        };
    }

    /**
     * Get the description for the setting type
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::TEXT => 'Single line text input for short text values',
            self::TEXTAREA => 'Multi-line text area for longer text content',
            self::CHECKBOX => 'Boolean checkbox for true/false settings',
            self::RADIO => 'Radio button group for single choice selection',
            self::SELECT => 'Dropdown select with predefined options',
            self::FILE => 'File upload input for document files',
            self::MEDIA => 'Media picker for images, videos, and other media',
            self::COLOR => 'Color picker for selecting color values',
            self::DATE => 'Date picker for selecting calendar dates',
            self::DATETIME => 'Date and time picker for precise timestamps',
            self::EMAIL => 'Email input with built-in email validation',
            self::NUMBER => 'Numeric input for number values',
            self::PASSWORD => 'Secure password input with visibility toggle',
            self::RANGE => 'Range slider for numeric value selection',
            self::TEL => 'Telephone input for phone number entry',
            self::TIME => 'Time picker for selecting time values',
            self::URL => 'URL input for web address entry',
            self::REPEATER => 'Repeater field for multiple similar entries',
        };
    }

    /**
     * Check if the setting type is a text input
     */
    public function isTextInput(): bool
    {
        return in_array($this, [self::TEXT, self::TEXTAREA, self::EMAIL, self::PASSWORD, self::TEL, self::URL]);
    }

    /**
     * Check if the setting type is a numeric input
     */
    public function isNumericInput(): bool
    {
        return in_array($this, [self::NUMBER, self::RANGE]);
    }

    /**
     * Check if the setting type is a date/time input
     */
    public function isDateTimeInput(): bool
    {
        return in_array($this, [self::DATE, self::DATETIME, self::TIME]);
    }

    /**
     * Check if the setting type is a file/media input
     */
    public function isFileInput(): bool
    {
        return in_array($this, [self::FILE, self::MEDIA]);
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
