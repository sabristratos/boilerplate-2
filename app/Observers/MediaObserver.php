<?php

namespace App\Observers;

use App\Jobs\OptimizeImageJob;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    /**
     * Handle the Media "created" event.
     */
    public function created(Media $media): void
    {
        // Only optimize image files
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));

        if (in_array($extension, $imageExtensions)) {
            // Dispatch the optimization job
            OptimizeImageJob::dispatch($media)->onQueue('image-optimization');
        }
    }

    /**
     * Handle the Media "updated" event.
     */
    public function updated(Media $media): void
    {
        // Re-optimize if the file was replaced
        if ($media->wasChanged('file_name')) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));

            if (in_array($extension, $imageExtensions)) {
                OptimizeImageJob::dispatch($media)->onQueue('image-optimization');
            }
        }
    }

    /**
     * Handle the Media "deleted" event.
     */
    public function deleted(Media $media): void
    {
        // Clean up any optimization-related data if needed
    }
}
