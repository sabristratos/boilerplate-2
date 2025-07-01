<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Media $media
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OptimizerChain $optimizerChain): void
    {
        $path = $this->media->getPath();
        
        if (!file_exists($path)) {
            return;
        }

        // Only optimize image files
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $imageExtensions)) {
            return;
        }

        try {
            $optimizerChain->optimize($path);
            
            // Log the optimization
            \Log::info('Image optimized', [
                'media_id' => $this->media->id,
                'file_name' => $this->media->file_name,
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            \Log::error('Image optimization failed', [
                'media_id' => $this->media->id,
                'file_name' => $this->media->file_name,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Image optimization job failed', [
            'media_id' => $this->media->id,
            'file_name' => $this->media->file_name,
            'error' => $exception->getMessage(),
        ]);
    }
} 