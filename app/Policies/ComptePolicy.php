<?php

namespace App\Policies;

use App\Models\Compte;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComptePolicy
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
    public function view(User $user, Compte $compte): bool
    {
        //Les membres n'ont accès qu'à leurs propres comptes
        if ($user->hasRole('membre')) {
            return $user->id === $compte->user_id;
        }
        return true; //Les agents ont accès à tous les comptes
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
    public function update(User $user, Compte $compte): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Compte $compte): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Compte $compte): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Compte $compte): bool
    {
        return false;
    }
}
