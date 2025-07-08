<?php

declare(strict_types=1);

namespace App\Enums;

enum FormStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    /**
     * Get all available statuses as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::PUBLISHED->value => 'Published',
            self::ARCHIVED->value => 'Archived',
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
            self::ARCHIVED => 'Archived',
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
            self::ARCHIVED => 'zinc',
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
            self::ARCHIVED => 'archive-box',
        };
    }

    /**
     * Get the description for the status
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Form is in draft mode and not accessible to users',
            self::PUBLISHED => 'Form is published and accessible to users for submissions',
            self::ARCHIVED => 'Form is archived and no longer accepting submissions',
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

    /**
     * Check if the status is archived
     */
    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
} 