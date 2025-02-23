<?php

namespace Modules\Taxido\Policies;

use App\Models\User;
use Modules\Taxido\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Taxido\Enums\RoleEnum;

class DriverPolicy
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
        if ($user->can('driver.index') && ($user?->role?->name != RoleEnum::DRIVER || $user->id == $user->id)) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  Modules\Taxido\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Driver $driver)
    {
        if ($user->can('driver.index') && ($user?->role?->name != RoleEnum::DRIVER || $user->id == $driver->id)) {
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
        if ($user->can('driver.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  Modules\Taxido\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Driver $driver)
    {
        if (
            $user->can('driver.edit')
            && ($user?->role?->name != RoleEnum::DRIVER || $user->id == $driver->created_by_id)
        ) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  Modules\Taxido\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Driver $driver)
    {
        if ($user->can('driver.destroy') && $user->id == $driver->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  Modules\Taxido\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Driver $driver)
    {
        if ($user->can('driver.restore') && $user->id == $driver->created_by_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  Modules\Taxido\Models\Driver  $driver
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Driver $driver)
    {
        if ($user->can('driver.forceDelete') && $user->id == $driver->created_by_id) {
            return true;
        }
    }
}
