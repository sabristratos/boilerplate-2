<?php

namespace App\Policies;

use App\Models\ContentBlock;
use App\Models\User;

class ContentBlockPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContentBlock $contentBlock): bool
    {
        return $user->can('pages.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('pages.edit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContentBlock $contentBlock): bool
    {
        return $user->can('pages.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContentBlock $contentBlock): bool
    {
        return $user->can('pages.edit');
    }
}
