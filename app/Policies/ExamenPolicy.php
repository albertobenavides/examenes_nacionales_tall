<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Examen;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamenPolicy
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
        return $user->can('view_any_examen');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function view(User $user, Examen $examen): bool
    {
        return $user->can('view_examen');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_examen');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function update(User $user, Examen $examen): bool
    {
        return $user->can('update_examen');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function delete(User $user, Examen $examen): bool
    {
        return $user->can('delete_examen');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_examen');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function forceDelete(User $user, Examen $examen): bool
    {
        return $user->can('force_delete_examen');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_examen');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function restore(User $user, Examen $examen): bool
    {
        return $user->can('restore_examen');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_examen');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Examen  $examen
     * @return bool
     */
    public function replicate(User $user, Examen $examen): bool
    {
        return $user->can('replicate_examen');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_examen');
    }

}
