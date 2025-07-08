<?php

declare(strict_types=1);

namespace App\Enums;

enum PublishStatus: string
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
    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'amber',
            self::PUBLISHED => 'lime',
        };
    }

    /**
     * Get the icon for the status
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'document-text',
            self::PUBLISHED => 'check-circle',
        };
    }

    /**
     * Get the description for the status
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Content is in draft mode and not visible to the public',
            self::PUBLISHED => 'Content is published and visible to the public',
        };
    }

    /**
     * Check if the status is published
     */
    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Check if the status is draft
     */
    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
