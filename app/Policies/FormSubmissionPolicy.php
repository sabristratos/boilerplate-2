<?php

namespace App\Policies;

use App\Models\FormSubmission;
use App\Models\User;

class FormSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view form submissions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('view form submissions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Anyone can submit forms
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('edit form submissions');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FormSubmission $formSubmission): bool
    {
        return $user->hasPermissionTo('delete form submissions');
    }
} 