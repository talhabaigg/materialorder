<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RequisitionLineItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequisitionLineItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_requisition::line::item');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('view_requisition::line::item');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_requisition::line::item');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('update_requisition::line::item');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('delete_requisition::line::item');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_requisition::line::item');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('force_delete_requisition::line::item');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_requisition::line::item');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('restore_requisition::line::item');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_requisition::line::item');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, RequisitionLineItem $requisitionLineItem): bool
    {
        return $user->can('replicate_requisition::line::item');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_requisition::line::item');
    }
}
