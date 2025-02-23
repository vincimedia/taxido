<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Language;

class LanguagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('language.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Language $language)
    {
        if ($user->can('language.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('language.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Language $language)
    {
        if ($user->can('language.edit') && $user->id == $language->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Language $language)
    {
        if ($user->can('language.destroy') && $user->id == $language->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Language $language)
    {
        if ($user->can('language.restore') && $user->id == $language->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Language $language)
    {
        if ($user->can('language.forceDelete') && $user->id == $language->created_by_id) {
            return true;
        }
    }
}
