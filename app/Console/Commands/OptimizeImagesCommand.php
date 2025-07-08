<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeImageJob;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--force : Force re-optimization of all images} {--limit= : Limit the number of images to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all images in the media library';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting image optimization...');

        $query = Media::query();

        // Only process image files
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $query->whereIn('extension', $imageExtensions);

        // Apply limit if specified
        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $mediaItems = $query->get();

        if ($mediaItems->isEmpty()) {
            $this->info('No images found to optimize.');

            return Command::SUCCESS;
        }

        $this->info("Found {$mediaItems->count()} images to optimize.");

        $bar = $this->output->createProgressBar($mediaItems->count());
        $bar->start();

        $optimizedCount = 0;
        $failedCount = 0;

        foreach ($mediaItems as $media) {
            try {
                if ($this->option('force') || ! file_exists($media->getPath())) {
                    OptimizeImageJob::dispatch($media)->onQueue('image-optimization');
                    $optimizedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("\nFailed to optimize {$media->file_name}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Optimization complete!');
        $this->info("Successfully queued: {$optimizedCount} images");

        if ($failedCount > 0) {
            $this->warn("Failed to queue: {$failedCount} images");
        }

        $this->info('Images are being processed in the background. Check the queue worker logs for progress.');

        return Command::SUCCESS;
    }
}
