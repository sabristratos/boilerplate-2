<?php

namespace App\Policies;

use App\Models\ContentBlock;
use App\Models\User;

class ContentBlockPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContentBlock $contentBlock): bool
    {
        return $user->can('edit content');
    }
}
