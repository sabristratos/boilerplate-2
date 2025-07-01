<?php

namespace App\Policies;

use App\Models\User;

class BackupPolicy
{
    /**
     * Determine whether the user can create backups.
     */
    public function create(User $user, string $model): bool
    {
        return $user->hasPermissionTo('backup.create');
    }

    /**
     * Determine whether the user can download backups.
     */
    public function download(User $user, string $model): bool
    {
        return $user->hasPermissionTo('backup.download');
    }

    /**
     * Determine whether the user can delete backups.
     */
    public function delete(User $user, string $model): bool
    {
        return $user->hasPermissionTo('backup.delete');
    }

    /**
     * Determine whether the user can view backups.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('backup.view');
    }
} 