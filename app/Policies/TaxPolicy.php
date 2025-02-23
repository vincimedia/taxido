<?php

namespace App\Policies;

use App\Models\Tax;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->can('tax.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Tax $tax)
    {
        if ($user->can('tax.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('tax.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Tax $tax)
    {
        if ($user->can('tax.edit') && $user->id == $tax->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Tax $tax)
    {
        if ($user->can('tax.destroy') && $user->id == $tax->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Tax $tax)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Tax $tax)
    {
        //
    }
}
