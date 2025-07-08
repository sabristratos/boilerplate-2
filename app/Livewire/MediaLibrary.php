<?php

namespace App\Livewire;

use App\Enums\MediaType;
use App\Traits\WithEnumHelpers;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibrary extends Component
{
    use WithPagination, WithEnumHelpers;

    /**
     * The number of items to display per page.
     */
    public $perPage = 12;

    /**
     * The search query.
     */
    public $search = '';

    /**
     * The sort field.
     */
    public $sortField = 'created_at';

    /**
     * The sort direction.
     */
    public $sortDirection = 'desc';

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Set the sort field and direction.
     */
    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Delete a media item.
     */
    public function deleteMedia(int $id): void
    {
        try {
            $media = Media::findOrFail($id);
            $media->delete();

            Flux::toast('Media deleted successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to delete media: '.$e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Get the media type enum for a MIME type.
     */
    public function getMediaTypeForMime(string $mimeType): MediaType
    {
        return $this->getMediaType($mimeType);
    }

    /**
     * Get the appropriate icon for a MIME type using MediaType enum.
     */
    public function getIconForMimeType(string $mimeType): string
    {
        $mediaType = $this->getMediaType($mimeType);
        return $mediaType->getIcon();
    }

    /**
     * Get the color for a media type.
     */
    public function getColorForMimeType(string $mimeType): string
    {
        $mediaType = $this->getMediaType($mimeType);
        return $mediaType->getColor();
    }

    /**
     * Get the label for a media type.
     */
    public function getLabelForMimeType(string $mimeType): string
    {
        $mediaType = $this->getMediaType($mimeType);
        return $mediaType->label();
    }

    /**
     * Get the description for a media type.
     */
    public function getDescriptionForMimeType(string $mimeType): string
    {
        $mediaType = $this->getMediaType($mimeType);
        return $mediaType->getDescription();
    }

    /**
     * Format file size to human-readable format.
     */
    public function formatFileSize(int $bytes): string
    {
        $units = [__('media.units.B'), __('media.units.KB'), __('media.units.MB'), __('media.units.GB'), __('media.units.TB')];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $query = Media::query();

        if ($this->search) {
            $query->where(function ($q): void {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('file_name', 'like', '%'.$this->search.'%')
                    ->orWhere('mime_type', 'like', '%'.$this->search.'%');
            });
        }

        $media = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.media-library', [
            'media' => $media,
        ]);
    }
}
