<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingGroupKey: string
{
    case GENERAL = 'general';
    case APPEARANCE = 'appearance';
    case EMAIL = 'email';
    case SECURITY = 'security';
    case SOCIAL = 'social';
    case ADVANCED = 'advanced';
    case CONTACT = 'contact';
    case CONTENT = 'content';
    case NAVIGATION = 'navigation';
    case SEO = 'seo';

    /**
     * Get all available setting groups as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::GENERAL->value => 'General',
            self::APPEARANCE->value => 'Appearance',
            self::EMAIL->value => 'Email',
            self::SECURITY->value => 'Security',
            self::SOCIAL->value => 'Social',
            self::ADVANCED->value => 'Advanced',
            self::CONTACT->value => 'Contact',
            self::CONTENT->value => 'Content',
            self::NAVIGATION->value => 'Navigation',
            self::SEO->value => 'SEO',
        ];
    }

    /**
     * Get the label for the setting group
     */
    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'General',
            self::APPEARANCE => 'Appearance',
            self::EMAIL => 'Email',
            self::SECURITY => 'Security',
            self::SOCIAL => 'Social',
            self::ADVANCED => 'Advanced',
            self::CONTACT => 'Contact',
            self::CONTENT => 'Content',
            self::NAVIGATION => 'Navigation',
            self::SEO => 'SEO',
        };
    }

    /**
     * Get the color for the setting group
     */
    public function getColor(): string
    {
        return match ($this) {
            self::GENERAL => 'blue',
            self::APPEARANCE => 'purple',
            self::EMAIL => 'cyan',
            self::SECURITY => 'red',
            self::SOCIAL => 'pink',
            self::ADVANCED => 'amber',
            self::CONTACT => 'green',
            self::CONTENT => 'indigo',
            self::NAVIGATION => 'emerald',
            self::SEO => 'orange',
        };
    }

    /**
     * Get the icon for the setting group
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::GENERAL => 'cog-6-tooth',
            self::APPEARANCE => 'paint-brush',
            self::EMAIL => 'envelope',
            self::SECURITY => 'shield-check',
            self::SOCIAL => 'share',
            self::ADVANCED => 'wrench-screwdriver',
            self::CONTACT => 'phone',
            self::CONTENT => 'document-text',
            self::NAVIGATION => 'bars-3',
            self::SEO => 'magnifying-glass',
        };
    }

    /**
     * Get the description for the setting group
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::GENERAL => 'General application settings and configuration',
            self::APPEARANCE => 'Visual appearance and theme settings',
            self::EMAIL => 'Email configuration and notification settings',
            self::SECURITY => 'Security and authentication settings',
            self::SOCIAL => 'Social media integration settings',
            self::ADVANCED => 'Advanced technical settings for power users',
            self::CONTACT => 'Contact information and form settings',
            self::CONTENT => 'Content management and display settings',
            self::NAVIGATION => 'Navigation menu and structure settings',
            self::SEO => 'Search engine optimization settings',
        };
    }

    /**
     * Get the order for displaying setting groups
     */
    public function getOrder(): int
    {
        return match ($this) {
            self::GENERAL => 1,
            self::APPEARANCE => 2,
            self::EMAIL => 3,
            self::SECURITY => 4,
            self::CONTACT => 5,
            self::CONTENT => 6,
            self::NAVIGATION => 7,
            self::SOCIAL => 8,
            self::SEO => 9,
            self::ADVANCED => 10,
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
