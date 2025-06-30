<?php

namespace App\Enums;

enum ContentBlockStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    /**
     * Get all available statuses as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::PUBLISHED->value => 'Published',
        ];
    }

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
        };
    }

    /**
     * Get the color for the status badge
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'amber',
            self::PUBLISHED => 'lime',
        };
    }
}
