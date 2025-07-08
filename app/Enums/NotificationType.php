<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationType: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
    case INFO = 'info';

    /**
     * Get all available notification types as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::SUCCESS->value => 'Success',
            self::WARNING->value => 'Warning',
            self::ERROR->value => 'Error',
            self::INFO->value => 'Information',
        ];
    }

    /**
     * Get the label for the notification type
     */
    public function label(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success',
            self::WARNING => 'Warning',
            self::ERROR => 'Error',
            self::INFO => 'Information',
        };
    }

    /**
     * Get the color for the notification type
     */
    public function getColor(): string
    {
        return match ($this) {
            self::SUCCESS => 'lime',
            self::WARNING => 'amber',
            self::ERROR => 'red',
            self::INFO => 'blue',
        };
    }

    /**
     * Get the icon for the notification type
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::SUCCESS => 'check-circle',
            self::WARNING => 'exclamation-triangle',
            self::ERROR => 'x-circle',
            self::INFO => 'information-circle',
        };
    }

    /**
     * Get the CSS class for the notification type
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::SUCCESS => 'bg-lime-50 border-lime-200 text-lime-800',
            self::WARNING => 'bg-amber-50 border-amber-200 text-amber-800',
            self::ERROR => 'bg-red-50 border-red-200 text-red-800',
            self::INFO => 'bg-blue-50 border-blue-200 text-blue-800',
        };
    }

    /**
     * Get the description for the notification type
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success notifications for positive actions and confirmations',
            self::WARNING => 'Warning notifications for important alerts and cautions',
            self::ERROR => 'Error notifications for failed actions and critical issues',
            self::INFO => 'Information notifications for general updates and details',
        };
    }

    /**
     * Check if the notification type is a success message
     */
    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if the notification type is a warning message
     */
    public function isWarning(): bool
    {
        return $this === self::WARNING;
    }

    /**
     * Check if the notification type is an error message
     */
    public function isError(): bool
    {
        return $this === self::ERROR;
    }

    /**
     * Check if the notification type is an info message
     */
    public function isInfo(): bool
    {
        return $this === self::INFO;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
} 