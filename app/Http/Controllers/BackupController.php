<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function download(Request $request, string $filename)
    {
        // Authorize the user
        $this->authorize('download', 'backup');

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $path = config('backup.backup.name').'/'.$filename;

        if (! $disk->exists($path)) {
            abort(404, 'Backup file not found');
        }

        // Return the file as a download
        return $disk->download($path, $filename);
    }
}
