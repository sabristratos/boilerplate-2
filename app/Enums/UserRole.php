<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case USER = 'user';

    /**
     * Get all available roles as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::SUPER_ADMIN->value => 'Super Admin',
            self::ADMIN->value => 'Administrator',
            self::EDITOR->value => 'Editor',
            self::USER->value => 'User',
        ];
    }

    /**
     * Get the label for the role
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
            self::EDITOR => 'Editor',
            self::USER => 'User',
        };
    }

    /**
     * Get the color for the role badge
     */
    public function getColor(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'red',
            self::ADMIN => 'purple',
            self::EDITOR => 'blue',
            self::USER => 'zinc',
        };
    }

    /**
     * Get the icon for the role
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'shield-check',
            self::ADMIN => 'cog-6-tooth',
            self::EDITOR => 'pencil-square',
            self::USER => 'user',
        };
    }

    /**
     * Check if the role is an admin role
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }

    /**
     * Check if the role is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    /**
     * Get the description for the role
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super administrator with full system access and control',
            self::ADMIN => 'Administrator with extensive system management capabilities',
            self::EDITOR => 'Editor with content creation and modification permissions',
            self::USER => 'Regular user with basic access and limited permissions',
        };
    }

    /**
     * Check if the role is an editor
     */
    public function isEditor(): bool
    {
        return $this === self::EDITOR;
    }

    /**
     * Check if the role is a regular user
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Get the permission level for the role
     */
    public function getPermissionLevel(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 4,
            self::ADMIN => 3,
            self::EDITOR => 2,
            self::USER => 1,
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
} 