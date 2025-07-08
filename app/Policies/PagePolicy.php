<?php

namespace App\Policies;

use App\Enums\PublishStatus;
use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Page $page): bool
    {
        // Published pages are publicly viewable
        if ($page->status === PublishStatus::PUBLISHED) {
            return true;
        }

        // Draft pages require authentication and proper permissions
        if (!$user instanceof \App\Models\User) {
            return false;
        }

        return $user->can('pages.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('pages.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        return $user->can('pages.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->can('pages.delete');
    }
}
