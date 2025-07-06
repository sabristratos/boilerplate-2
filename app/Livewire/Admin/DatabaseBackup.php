<?php

namespace App\Livewire\Admin;

use App\Traits\WithToastNotifications;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\Backup\BackupDestination\Backup;

class DatabaseBackup extends Component
{
    use WithToastNotifications;

    public bool $isCreatingBackup = false;

    public bool $showDeleteModal = false;

    public ?string $backupToDelete = null;

    public string $backupStatus = '';

    public bool $backupSupported = true;

    public string $backupSupportMessage = '';

    public function mount()
    {
        $this->checkBackupSupport();
    }

    public function checkBackupSupport()
    {
        $this->backupSupported = true;
        $this->backupSupportMessage = '';

        // Check if we're using MySQL and if mysqldump is available
        if (config('database.default') === 'mysql') {
            $mysqldumpPath = $this->findMysqldump();

            if (! $mysqldumpPath) {
                $this->backupSupported = false;
                $this->backupSupportMessage = __('backup.mysql_not_supported');
            }
        }

        // Check if we're using SQLite (which is always supported)
        if (config('database.default') === 'sqlite') {
            $this->backupSupported = true;
            $this->backupSupportMessage = '';
        }

        // Check if we're using PostgreSQL and if pg_dump is available
        if (config('database.default') === 'pgsql') {
            $pgDumpPath = $this->findPgDump();

            if (! $pgDumpPath) {
                $this->backupSupported = false;
                $this->backupSupportMessage = __('backup.postgresql_not_supported');
            }
        }
    }

    private function findMysqldump()
    {
        // Check common Windows paths for mysqldump
        $possiblePaths = [
            'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe',
            'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe',
            'C:\xampp\mysql\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            'C:\wamp\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            'mysqldump', // Check if it's in PATH
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                return $path;
            }
        }

        return false;
    }

    private function findPgDump()
    {
        // Check common Windows paths for pg_dump
        $possiblePaths = [
            'C:\Program Files\PostgreSQL\15\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\14\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\13\bin\pg_dump.exe',
            'pg_dump', // Check if it's in PATH
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                return $path;
            }
        }

        return false;
    }

    private function commandExists($command)
    {
        $output = [];
        $returnCode = 0;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            exec("where $command 2>nul", $output, $returnCode);
        } else {
            // Unix/Linux/Mac
            exec("which $command 2>/dev/null", $output, $returnCode);
        }

        return $returnCode === 0;
    }

    public function createBackup()
    {
        $this->authorize('create', 'backup');

        if (! $this->backupSupported) {
            $this->showErrorToast($this->backupSupportMessage);

            return;
        }

        $this->isCreatingBackup = true;
        $this->backupStatus = '';

        try {
            // Run the database-only backup using Artisan
            $exitCode = \Artisan::call('backup:run', ['--only-db' => true]);
            $output = \Artisan::output();

            if ($exitCode === 0) {
                $this->backupStatus = 'success';
                $this->showSuccessToast(__('backup.created_successfully'));

                // Log the successful backup
                \Log::info('Database backup created successfully', [
                    'output' => $output,
                    'exit_code' => $exitCode,
                ]);
            } else {
                $this->backupStatus = 'error';
                $this->showErrorToast(__('backup.creation_failed').': '.$output);

                // Log the failed backup
                \Log::error('Database backup failed', [
                    'output' => $output,
                    'exit_code' => $exitCode,
                ]);
            }
        } catch (\Exception $e) {
            $this->backupStatus = 'error';
            $this->showErrorToast(__('backup.creation_failed').': '.$e->getMessage());

            // Log the exception
            \Log::error('Database backup exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->isCreatingBackup = false;
        }
    }

    public function downloadBackup(string $backupName)
    {
        $this->authorize('download', 'backup');

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $path = config('backup.backup.name').'/'.$backupName;

        if (! $disk->exists($path)) {
            $this->showErrorToast(__('backup.file_not_found'));

            return;
        }

        // Redirect to a dedicated download route
        return redirect()->route('admin.backup.download', ['filename' => $backupName]);
    }

    public function confirmDeleteBackup(string $backupName)
    {
        $this->backupToDelete = $backupName;
        $this->showDeleteModal = true;
    }

    public function deleteBackup()
    {
        $this->authorize('delete', 'backup');

        if (! $this->backupToDelete) {
            return;
        }

        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $path = config('backup.backup.name').'/'.$this->backupToDelete;

            if ($disk->exists($path)) {
                $disk->delete($path);
                $this->showSuccessToast(__('backup.deleted_successfully'));
            }
        } catch (\Exception $e) {
            $this->showErrorToast(__('backup.deletion_failed').': '.$e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->backupToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->backupToDelete = null;
    }

    public function getBackupsProperty()
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $path = config('backup.backup.name');

            if (! $disk->exists($path)) {
                return collect();
            }

            $files = $disk->files($path);

            return collect($files)
                ->filter(function ($file) {
                    // Look for both .sql files and .zip files (backup archives)
                    return str_ends_with($file, '.sql') || str_ends_with($file, '.zip');
                })
                ->map(function ($file) use ($disk) {
                    $filename = basename($file);
                    $size = $disk->size($file);
                    $date = $disk->lastModified($file);

                    return [
                        'name' => $filename,
                        'size' => $this->formatBytes($size),
                        'date' => $date,
                        'date_formatted' => date('Y-m-d H:i:s', $date),
                    ];
                })
                ->sortByDesc('date')
                ->values();
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error getting backups: '.$e->getMessage());

            return collect();
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    public function render()
    {
        return view('livewire.admin.database-backup')
            ->title(__('navigation.database_backup'));
    }
}
