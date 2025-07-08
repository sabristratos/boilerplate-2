<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\DTOs\MediaDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Interface for media service operations.
 *
 * This interface defines the contract for media-related business logic,
 * including upload, management, and retrieval operations.
 */
interface MediaServiceInterface
{
    /**
     * Upload media to a model.
     *
     * @param Model $model The model to attach media to
     * @param UploadedFile $file The uploaded file
     * @param string $collection The media collection name
     * @param array<string, mixed> $customProperties Custom properties for the media
     * @return MediaDTO The created media DTO
     */
    public function uploadMedia(Model $model, UploadedFile $file, string $collection = 'default', array $customProperties = []): MediaDTO;

    /**
     * Add media from URL to a model.
     *
     * @param Model $model The model to attach media to
     * @param string $url The URL to download media from
     * @param string $collection The media collection name
     * @param array<string, mixed> $customProperties Custom properties for the media
     * @return MediaDTO The created media DTO
     */
    public function addMediaFromUrl(Model $model, string $url, string $collection = 'default', array $customProperties = []): MediaDTO;

    /**
     * Copy existing media to a model.
     *
     * @param Model $model The model to attach media to
     * @param Media $media The media to copy
     * @param string $collection The media collection name
     * @return MediaDTO The copied media DTO
     */
    public function copyMedia(Model $model, Media $media, string $collection = 'default'): MediaDTO;

    /**
     * Delete media from a model.
     *
     * @param Model $model The model to remove media from
     * @param string $collection The media collection name
     * @return bool True if media was deleted successfully
     */
    public function deleteMedia(Model $model, string $collection = 'default'): bool;

    /**
     * Get media DTO for a model and collection.
     *
     * @param Model $model The model
     * @param string $collection The media collection name
     * @return MediaDTO|null The media DTO or null if not found
     */
    public function getMediaDTO(Model $model, string $collection = 'default'): ?MediaDTO;

    /**
     * Get all media DTOs for a model and collection.
     *
     * @param Model $model The model
     * @param string $collection The media collection name
     * @return array<MediaDTO> Array of media DTOs
     */
    public function getAllMediaDTOs(Model $model, string $collection = 'default'): array;

    /**
     * Search media by name or other criteria.
     *
     * @param string $search The search term
     * @param int $perPage The number of results per page
     * @param array<string, mixed> $filters Additional filters
     * @return \Illuminate\Pagination\LengthAwarePaginator The paginated media results
     */
    public function searchMedia(string $search = '', int $perPage = 12, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Optimize media files.
     *
     * @param Media $media The media to optimize
     * @return bool True if optimization was successful
     */
    public function optimizeMedia(Media $media): bool;

    /**
     * Generate media conversions.
     *
     * @param Media $media The media to generate conversions for
     * @param array<string> $conversions The conversion names to generate
     * @return bool True if conversions were generated successfully
     */
    public function generateConversions(Media $media, array $conversions = []): bool;

    /**
     * Get media statistics.
     *
     * @param Model|null $model Optional model to get statistics for
     * @return array<string, mixed> Media statistics
     */
    public function getMediaStatistics(?Model $model = null): array;
} 