<?php

namespace App\Policies;

use App\Models\CloturesComptable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CloturesComptablePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CloturesComptable $cloturesComptable): bool
    {
        if($user->can('can.level6')){ //Le PCA et autre
            return true;
        }elseif ($user->can('can.level4')){ //On choisit une permission que seul le sup et chef d'agence possède
            return $user->agence_id === $cloturesComptable->agence_id;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CloturesComptable $cloturesComptable): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CloturesComptable $cloturesComptable): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CloturesComptable $cloturesComptable): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CloturesComptable $cloturesComptable): bool
    {
        return false;
    }
}
