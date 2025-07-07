<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object for Media file data.
 *
 * This DTO encapsulates all data related to a media file, including
 * file information, metadata, conversions, and collection details.
 * It provides type-safe access to media properties and includes
 * validation and transformation methods.
 */
class MediaDTO extends BaseDTO
{
    /**
     * Create a new MediaDTO instance.
     *
     * @param int|null $id The media ID
     * @param string $fileName The original file name
     * @param string $name The media name
     * @param string $mimeType The MIME type
     * @param int $size The file size in bytes
     * @param string $disk The storage disk
     * @param string $path The file path
     * @param string $collectionName The collection name
     * @param string $modelType The model type
     * @param int|null $modelId The model ID
     * @param array<string, mixed>|null $customProperties Custom properties
     * @param array<string, mixed>|null $responsiveImages Responsive image data
     * @param array<string, mixed>|null $conversions Media conversions
     * @param Carbon|null $createdAt When the media was created
     * @param Carbon|null $updatedAt When the media was last updated
     * @param string|null $uuid The media UUID
     * @param string|null $conversionsDisk The conversions disk
     * @param array<string, mixed>|null $manipulations Media manipulations
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $fileName,
        public readonly string $name,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $disk,
        public readonly string $path,
        public readonly string $collectionName,
        public readonly string $modelType,
        public readonly ?int $modelId,
        public readonly ?array $customProperties = null,
        public readonly ?array $responsiveImages = null,
        public readonly ?array $conversions = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?string $uuid = null,
        public readonly ?string $conversionsDisk = null,
        public readonly ?array $manipulations = null,
    ) {
    }

    /**
     * Create a MediaDTO from an array.
     *
     * @param array<string, mixed> $data The array data
     * @return self The created DTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            fileName: $data['file_name'],
            name: $data['name'],
            mimeType: $data['mime_type'],
            size: $data['size'],
            disk: $data['disk'],
            path: $data['path'],
            collectionName: $data['collection_name'],
            modelType: $data['model_type'],
            modelId: $data['model_id'] ?? null,
            customProperties: $data['custom_properties'] ?? null,
            responsiveImages: $data['responsive_images'] ?? null,
            conversions: $data['conversions'] ?? null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            uuid: $data['uuid'] ?? null,
            conversionsDisk: $data['conversions_disk'] ?? null,
            manipulations: $data['manipulations'] ?? null,
        );
    }

    /**
     * Create a MediaDTO from a Spatie MediaLibrary Media model.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media $media The media model
     * @return self The created DTO
     */
    public static function fromModel(\Spatie\MediaLibrary\MediaCollections\Models\Media $media): self
    {
        return new self(
            id: $media->id,
            fileName: $media->file_name,
            name: $media->name,
            mimeType: $media->mime_type,
            size: $media->size,
            disk: $media->disk,
            path: $media->getPath(),
            collectionName: $media->collection_name,
            modelType: $media->model_type,
            modelId: $media->model_id,
            customProperties: $media->custom_properties,
            responsiveImages: $media->responsive_images,
            conversions: $media->conversions,
            createdAt: $media->created_at,
            updatedAt: $media->updated_at,
            uuid: $media->uuid,
            conversionsDisk: $media->conversions_disk,
            manipulations: $media->manipulations,
        );
    }

    /**
     * Create a MediaDTO for creating a new media file.
     *
     * @param string $fileName The original file name
     * @param string $name The media name
     * @param string $mimeType The MIME type
     * @param int $size The file size
     * @param string $disk The storage disk
     * @param string $path The file path
     * @param string $collectionName The collection name
     * @param string $modelType The model type
     * @param int|null $modelId The model ID
     * @return self The created DTO
     */
    public static function forCreation(
        string $fileName,
        string $name,
        string $mimeType,
        int $size,
        string $disk,
        string $path,
        string $collectionName,
        string $modelType,
        ?int $modelId = null,
    ): self {
        return new self(
            id: null,
            fileName: $fileName,
            name: $name,
            mimeType: $mimeType,
            size: $size,
            disk: $disk,
            path: $path,
            collectionName: $collectionName,
            modelType: $modelType,
            modelId: $modelId,
        );
    }

    /**
     * Get the file extension.
     *
     * @return string The file extension
     */
    public function getExtension(): string
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }

    /**
     * Get the file name without extension.
     *
     * @return string The file name without extension
     */
    public function getFileNameWithoutExtension(): string
    {
        return pathinfo($this->fileName, PATHINFO_FILENAME);
    }

    /**
     * Check if the media is an image.
     *
     * @return bool True if the media is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    /**
     * Check if the media is a video.
     *
     * @return bool True if the media is a video
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mimeType, 'video/');
    }

    /**
     * Check if the media is an audio file.
     *
     * @return bool True if the media is an audio file
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mimeType, 'audio/');
    }

    /**
     * Check if the media is a document.
     *
     * @return bool True if the media is a document
     */
    public function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];

        return in_array($this->mimeType, $documentTypes, true);
    }

    /**
     * Get the file size in a human-readable format.
     *
     * @return string The formatted file size
     */
    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get a custom property value.
     *
     * @param string $key The property key
     * @param mixed $default The default value if property doesn't exist
     * @return mixed The property value
     */
    public function getCustomProperty(string $key, mixed $default = null): mixed
    {
        return $this->customProperties[$key] ?? $default;
    }

    /**
     * Check if a custom property exists.
     *
     * @param string $key The property key
     * @return bool True if the property exists
     */
    public function hasCustomProperty(string $key): bool
    {
        return isset($this->customProperties[$key]);
    }

    /**
     * Get the URL for a specific conversion.
     *
     * @param string $conversionName The conversion name
     * @return string|null The conversion URL
     */
    public function getConversionUrl(string $conversionName): ?string
    {
        if ($this->conversions === null || ! isset($this->conversions[$conversionName])) {
            return null;
        }

        return $this->conversions[$conversionName]['url'] ?? null;
    }

    /**
     * Check if a conversion exists.
     *
     * @param string $conversionName The conversion name
     * @return bool True if the conversion exists
     */
    public function hasConversion(string $conversionName): bool
    {
        return $this->conversions !== null && isset($this->conversions[$conversionName]);
    }

    /**
     * Get all available conversion names.
     *
     * @return array<string> The conversion names
     */
    public function getConversionNames(): array
    {
        if ($this->conversions === null) {
            return [];
        }

        return array_keys($this->conversions);
    }

    /**
     * Get the full URL to the media file.
     *
     * @return string The full URL
     */
    public function getUrl(): string
    {
        return config('app.url') . '/storage/' . $this->path;
    }

    /**
     * Get the media age in days.
     *
     * @return int|null The age in days, or null if no creation date
     */
    public function getAgeInDays(): ?int
    {
        if ($this->createdAt === null) {
            return null;
        }

        return $this->createdAt->diffInDays(now());
    }

    /**
     * Check if the media is recent (within the last 24 hours).
     *
     * @return bool True if the media is recent
     */
    public function isRecent(): bool
    {
        if ($this->createdAt === null) {
            return false;
        }

        return $this->createdAt->isAfter(now()->subDay());
    }

    /**
     * Get the media as an array.
     *
     * @return array<string, mixed> The media data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->fileName,
            'name' => $this->name,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'disk' => $this->disk,
            'path' => $this->path,
            'collection_name' => $this->collectionName,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'custom_properties' => $this->customProperties,
            'responsive_images' => $this->responsiveImages,
            'conversions' => $this->conversions,
            'created_at' => $this->createdAt?->toISOString(),
            'updated_at' => $this->updatedAt?->toISOString(),
            'uuid' => $this->uuid,
            'conversions_disk' => $this->conversionsDisk,
            'manipulations' => $this->manipulations,
        ];
    }



    /**
     * Create a copy of this DTO with updated values.
     *
     * @param array<string, mixed> $changes The changes to apply
     * @return self A new DTO with the changes applied
     */
    public function with(array $changes): self
    {
        return new self(
            id: $changes['id'] ?? $this->id,
            fileName: $changes['file_name'] ?? $changes['fileName'] ?? $this->fileName,
            name: $changes['name'] ?? $this->name,
            mimeType: $changes['mime_type'] ?? $changes['mimeType'] ?? $this->mimeType,
            size: $changes['size'] ?? $this->size,
            disk: $changes['disk'] ?? $this->disk,
            path: $changes['path'] ?? $this->path,
            collectionName: $changes['collection_name'] ?? $changes['collectionName'] ?? $this->collectionName,
            modelType: $changes['model_type'] ?? $changes['modelType'] ?? $this->modelType,
            modelId: $changes['model_id'] ?? $changes['modelId'] ?? $this->modelId,
            customProperties: $changes['custom_properties'] ?? $changes['customProperties'] ?? $this->customProperties,
            responsiveImages: $changes['responsive_images'] ?? $changes['responsiveImages'] ?? $this->responsiveImages,
            conversions: $changes['conversions'] ?? $this->conversions,
            createdAt: $changes['created_at'] ?? $changes['createdAt'] ?? $this->createdAt,
            updatedAt: $changes['updated_at'] ?? $changes['updatedAt'] ?? $this->updatedAt,
            uuid: $changes['uuid'] ?? $this->uuid,
            conversionsDisk: $changes['conversions_disk'] ?? $changes['conversionsDisk'] ?? $this->conversionsDisk,
            manipulations: $changes['manipulations'] ?? $this->manipulations,
        );
    }

    /**
     * Validate the DTO data.
     *
     * @return array<string, string> Validation errors, empty if valid
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->fileName)) {
            $errors['file_name'] = 'File name is required';
        }

        if (empty($this->name)) {
            $errors['name'] = 'Media name is required';
        }

        if (empty($this->mimeType)) {
            $errors['mime_type'] = 'MIME type is required';
        }

        if ($this->size <= 0) {
            $errors['size'] = 'Valid file size is required';
        }

        if (empty($this->disk)) {
            $errors['disk'] = 'Storage disk is required';
        }

        if (empty($this->path)) {
            $errors['path'] = 'File path is required';
        }

        if (empty($this->collectionName)) {
            $errors['collection_name'] = 'Collection name is required';
        }

        if (empty($this->modelType)) {
            $errors['model_type'] = 'Model type is required';
        }

        return $errors;
    }


} 