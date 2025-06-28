<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Flux\Flux;

class MediaLibrary extends Component
{
    use WithPagination;

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
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Set the sort field and direction.
     */
    public function sortBy($field)
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
    public function deleteMedia(int $id)
    {
        try {
            $media = Media::findOrFail($id);
            $media->delete();

            Flux::toast('Media deleted successfully.', variant: 'success');
        } catch (\Exception $e) {
            Flux::toast('Failed to delete media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Get the appropriate icon for a MIME type.
     */
    public function getIconForMimeType(string $mimeType): string
    {
        if (str_contains($mimeType, 'image')) {
            return 'photo';
        } elseif (str_contains($mimeType, 'video')) {
            return 'film';
        } elseif (str_contains($mimeType, 'audio')) {
            return 'musical-note';
        } elseif (str_contains($mimeType, 'pdf')) {
            return 'document-text';
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
            return 'document';
        } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
            return 'table-cells';
        } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
            return 'presentation-chart-bar';
        } elseif (str_contains($mimeType, 'zip') || str_contains($mimeType, 'archive')) {
            return 'archive-box';
        } else {
            return 'document';
        }
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

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $query = Media::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('file_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mime_type', 'like', '%' . $this->search . '%');
            });
        }

        $media = $query->orderBy($this->sortField, $this->sortDirection)
                      ->paginate($this->perPage);

        return view('livewire.media-library', [
            'media' => $media,
        ]);
    }
}
