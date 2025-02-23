<?php

namespace Modules\Taxido\Policies;

use App\Models\User;
use Modules\Taxido\Models\Notice;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticePolicy
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
        if ($user->can('notice.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Notice $notice)
    {
        if ($user->can('notice.index')) {
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
        if ($user->can('notice.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user,  Notice $notice)
    {
        if ($user->can('notice.edit') && $user->id == $notice->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user,  Notice $notice)
    {
        if ($user->can('notice.destroy') && $user->id == $notice->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, notice  $notice)
    {
        if ($user->can('notice.restore') && $user->id == $notice->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, notice  $notice)
    {
        if ($user->can('notice.forceDelete') && $user->id == $notice->created_by_id) {
            return true;
        }
    }
}
