<?php

namespace App\Policies;

use App\Models\Agence;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgencePolicy
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
    public function view(User $user, Agence $agence): bool
    {
        if ($user->can('agence.view.all')) { //On suppose que c'est le PCA ou Directrice Regionale
            return true;
        }elseif ($user->can('credit.cloturer')){ //On choisit une permission que seul le sup et chef d'agence possède
            return $user->agence_id === $agence->id;
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
    public function update(User $user, Agence $agence): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Agence $agence): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Agence $agence): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Agence $agence): bool
    {
        return false;
    }
}
