<?php

declare(strict_types=1);

namespace App\Enums;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case ARCHIVE = 'archive';
    case OTHER = 'other';

    /**
     * Get all available media types as an array for select inputs
     */
    public static function options(): array
    {
        return [
            self::IMAGE->value => 'Image',
            self::VIDEO->value => 'Video',
            self::AUDIO->value => 'Audio',
            self::DOCUMENT->value => 'Document',
            self::ARCHIVE->value => 'Archive',
            self::OTHER->value => 'Other',
        ];
    }

    /**
     * Get the label for the media type
     */
    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Image',
            self::VIDEO => 'Video',
            self::AUDIO => 'Audio',
            self::DOCUMENT => 'Document',
            self::ARCHIVE => 'Archive',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get the icon for the media type
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::IMAGE => 'photo',
            self::VIDEO => 'video-camera',
            self::AUDIO => 'musical-note',
            self::DOCUMENT => 'document',
            self::ARCHIVE => 'archive-box',
            self::OTHER => 'document-text',
        };
    }

    /**
     * Get the color for the media type
     */
    public function getColor(): string
    {
        return match ($this) {
            self::IMAGE => 'blue',
            self::VIDEO => 'purple',
            self::AUDIO => 'green',
            self::DOCUMENT => 'amber',
            self::ARCHIVE => 'zinc',
            self::OTHER => 'gray',
        };
    }

    /**
     * Get the MIME type patterns for this media type
     */
    public function mimePatterns(): array
    {
        return match ($this) {
            self::IMAGE => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/svg+xml',
                'image/bmp',
                'image/tiff',
            ],
            self::VIDEO => [
                'video/mp4',
                'video/avi',
                'video/mov',
                'video/wmv',
                'video/flv',
                'video/webm',
                'video/mkv',
            ],
            self::AUDIO => [
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'audio/mp4',
                'audio/aac',
                'audio/flac',
            ],
            self::DOCUMENT => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
            ],
            self::ARCHIVE => [
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/gzip',
                'application/x-tar',
            ],
            self::OTHER => [],
        };
    }

    /**
     * Check if a MIME type matches this media type
     */
    public function matchesMimeType(string $mimeType): bool
    {
        return in_array($mimeType, $this->mimePatterns());
    }

    /**
     * Get the media type from a MIME type
     */
    public static function fromMimeType(string $mimeType): self
    {
        foreach (self::cases() as $type) {
            if ($type->matchesMimeType($mimeType)) {
                return $type;
            }
        }

        return self::OTHER;
    }

    /**
     * Get the description for the media type
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::IMAGE => 'Image files including photos, graphics, and visual content',
            self::VIDEO => 'Video files for multimedia content and presentations',
            self::AUDIO => 'Audio files for music, podcasts, and sound content',
            self::DOCUMENT => 'Document files including PDFs, Word docs, and spreadsheets',
            self::ARCHIVE => 'Compressed archive files like ZIP and RAR',
            self::OTHER => 'Other file types not categorized above',
        };
    }

    /**
     * Check if the media type is an image
     */
    public function isImage(): bool
    {
        return $this === self::IMAGE;
    }

    /**
     * Check if the media type is a video
     */
    public function isVideo(): bool
    {
        return $this === self::VIDEO;
    }

    /**
     * Check if the media type is an audio file
     */
    public function isAudio(): bool
    {
        return $this === self::AUDIO;
    }

    /**
     * Check if the media type is a document
     */
    public function isDocument(): bool
    {
        return $this === self::DOCUMENT;
    }

    /**
     * Check if the media type is an archive
     */
    public function isArchive(): bool
    {
        return $this === self::ARCHIVE;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
} 