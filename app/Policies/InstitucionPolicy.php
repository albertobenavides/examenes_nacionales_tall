<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Institucion;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstitucionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_institucion');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function view(User $user, Institucion $institucion): bool
    {
        return $user->can('view_institucion');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_institucion');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function update(User $user, Institucion $institucion): bool
    {
        return $user->can('update_institucion');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function delete(User $user, Institucion $institucion): bool
    {
        return $user->can('delete_institucion');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_institucion');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function forceDelete(User $user, Institucion $institucion): bool
    {
        return $user->can('force_delete_institucion');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_institucion');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function restore(User $user, Institucion $institucion): bool
    {
        return $user->can('restore_institucion');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_institucion');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Institucion  $institucion
     * @return bool
     */
    public function replicate(User $user, Institucion $institucion): bool
    {
        return $user->can('replicate_institucion');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_institucion');
    }

}
