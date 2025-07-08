<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\MediaDTO;
use App\DTOs\DTOFactory;
use App\Services\Contracts\MediaServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Media service implementation.
 *
 * This service provides media handling functionality using Spatie Media Library.
 * It implements the MediaServiceInterface contract and provides methods for
 * uploading, managing, and retrieving media files.
 */
class MediaService implements MediaServiceInterface
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
    public function uploadMedia(Model $model, UploadedFile $file, string $collection = 'default', array $customProperties = []): MediaDTO
    {
        $media = $model->addMedia($file)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);

        return DTOFactory::createMediaDTO($media);
    }

    /**
     * Add media from URL to a model.
     *
     * @param Model $model The model to attach media to
     * @param string $url The URL to download media from
     * @param string $collection The media collection name
     * @param array<string, mixed> $customProperties Custom properties for the media
     * @return MediaDTO The created media DTO
     */
    public function addMediaFromUrl(Model $model, string $url, string $collection = 'default', array $customProperties = []): MediaDTO
    {
        $media = $model->addMediaFromUrl($url)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);

        return DTOFactory::createMediaDTO($media);
    }

    /**
     * Copy existing media to a model.
     *
     * @param Model $model The model to attach media to
     * @param Media $media The media to copy
     * @param string $collection The media collection name
     * @return MediaDTO The copied media DTO
     */
    public function copyMedia(Model $model, Media $media, string $collection = 'default'): MediaDTO
    {
        $copiedMedia = $media->copy($model, $collection);

        return DTOFactory::createMediaDTO($copiedMedia);
    }

    /**
     * Delete media from a model.
     *
     * @param Model $model The model to remove media from
     * @param string $collection The media collection name
     * @return bool True if media was deleted successfully
     */
    public function deleteMedia(Model $model, string $collection = 'default'): bool
    {
        $model->clearMediaCollection($collection);
        return true;
    }

    /**
     * Get media DTO for a model and collection.
     *
     * @param Model $model The model
     * @param string $collection The media collection name
     * @return MediaDTO|null The media DTO or null if not found
     */
    public function getMediaDTO(Model $model, string $collection = 'default'): ?MediaDTO
    {
        $media = $model->getFirstMedia($collection);
        
        if (!$media) {
            return null;
        }

        return DTOFactory::createMediaDTO($media);
    }

    /**
     * Get all media DTOs for a model and collection.
     *
     * @param Model $model The model
     * @param string $collection The media collection name
     * @return array<MediaDTO> Array of media DTOs
     */
    public function getAllMediaDTOs(Model $model, string $collection = 'default'): array
    {
        $mediaCollection = $model->getMedia($collection);
        
        return $mediaCollection->map(function ($media) {
            return DTOFactory::createMediaDTO($media);
        })->toArray();
    }

    /**
     * Search media by name or other criteria.
     *
     * @param string $search The search term
     * @param int $perPage The number of results per page
     * @param array<string, mixed> $filters Additional filters
     * @return LengthAwarePaginator The paginated media results
     */
    public function searchMedia(string $search = '', int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $query = Media::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
        }

        // Apply additional filters
        if (isset($filters['collection'])) {
            $query->where('collection_name', $filters['collection']);
        }

        if (isset($filters['mime_type'])) {
            $query->where('mime_type', 'like', "%{$filters['mime_type']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Optimize media files.
     *
     * @param Media $media The media to optimize
     * @return bool True if optimization was successful
     */
    public function optimizeMedia(Media $media): bool
    {
        // This is a placeholder implementation
        // In a real application, you would implement image optimization here
        return true;
    }

    /**
     * Generate media conversions.
     *
     * @param Media $media The media to generate conversions for
     * @param array<string> $conversions The conversion names to generate
     * @return bool True if conversions were generated successfully
     */
    public function generateConversions(Media $media, array $conversions = []): bool
    {
        try {
            if (empty($conversions)) {
                $media->generateConversions();
            } else {
                foreach ($conversions as $conversion) {
                    $media->generateConversion($conversion);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get media statistics.
     *
     * @param Model|null $model Optional model to get statistics for
     * @return array<string, mixed> Media statistics
     */
    public function getMediaStatistics(?Model $model = null): array
    {
        $query = Media::query();

        if ($model) {
            $query->where('model_type', get_class($model))
                  ->where('model_id', $model->id);
        }

        $totalMedia = $query->count();
        $totalSize = $query->sum('size');
        $collections = $query->distinct('collection_name')->pluck('collection_name')->toArray();

        return [
            'total_media' => $totalMedia,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'collections' => $collections,
            'collections_count' => count($collections),
        ];
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes The number of bytes
     * @return string The formatted size
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
} 