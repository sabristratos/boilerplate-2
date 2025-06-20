<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Flux\Flux;

class MediaDetail extends Component
{
    /**
     * The media ID.
     */
    public $mediaId;

    /**
     * Mount the component.
     */
    public function mount($id)
    {
        $this->mediaId = $id;
    }

    /**
     * Delete the media item.
     */
    public function deleteMedia()
    {
        try {
            $media = Media::findOrFail($this->mediaId);
            $media->delete();

            Flux::toast('Media deleted successfully.', variant: 'success');

            return redirect()->route('media.index');
        } catch (\Exception $e) {
            Flux::toast('Failed to delete media: ' . $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * Format file size to human-readable format.
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
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
     * Render the component.
     */
    public function render()
    {
        $media = Media::findOrFail($this->mediaId);

        return view('livewire.media-detail', [
            'media' => $media,
        ]);
    }
}
