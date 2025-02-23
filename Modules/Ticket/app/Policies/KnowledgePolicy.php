<?php

namespace Modules\Ticket\Policies;

use App\Models\User;
use Modules\Ticket\Models\Knowledge;
use Illuminate\Auth\Access\HandlesAuthorization;

class KnowledgePolicy
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
        if ($user->can('ticket.knowledge.index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Knowledge  $knowledge
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Knowledge $knowledge)
    {
        if ($user->can('ticket.knowledge.index')) {
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
        if ($user->can('ticket.knowledge.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Knowledge  $knowledge
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Knowledge $knowledge)
    {
        if ($user->can('ticket.knowledge.edit') && $user->id == $knowledge->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Knowledge  $knowledge
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Knowledge $knowledge)
    {
        if ($user->can('ticket.knowledge.destroy') && $user->id == $knowledge->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Knowledge  $knowledge
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Knowledge $knowledge)
    {
        if ($user->can('ticket.knowledge.restore') && $user->id == $knowledge->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Knowledge  $knowledge
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Knowledge $knowledge)
    {
        if ($user->can('ticket.knowledge.forceDelete') && $user->id == $knowledge->created_by_id) {
            return true;
        }
    }
}
