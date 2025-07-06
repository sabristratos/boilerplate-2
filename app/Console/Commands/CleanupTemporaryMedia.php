<?php

namespace App\Console\Commands;

use App\Models\TemporaryMedia;
use Illuminate\Console\Command;

class CleanupTemporaryMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanup-temporary {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old temporary media records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = TemporaryMedia::where('created_at', '<', now()->subDay());

        if ($this->option('dry-run')) {
            $count = $query->count();
            $this->info("Would delete {$count} temporary media records older than 24 hours.");

            return Command::SUCCESS;
        }

        $count = $query->delete();
        $this->info("Deleted {$count} temporary media records older than 24 hours.");

        return Command::SUCCESS;
    }
}
