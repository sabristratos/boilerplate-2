<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view forms');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('view forms') && $form->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create forms');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('edit forms') && $form->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Form $form): bool
    {
        return $user->hasPermissionTo('delete forms') && $form->user_id === $user->id;
    }
}
