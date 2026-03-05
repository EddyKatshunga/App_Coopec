<?php

namespace App\Models\Traits;

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToAgence
{
    protected static function bootBelongsToAgence()
    {
        static::addGlobalScope('agence_security', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // Niveau 3 : Super Admin / Toutes les agences
                if ($user->can('agence.view.all')) {
                    return; // On ne filtre rien
                }

                // Niveau 2 : Vue Agence (mais restreint à la sienne)
                if ($user->can('agence.operations.view')) {
                    $builder->where('agence_id', $user->agence_id);
                    return;
                }

                // Niveau 1 : Vue Personnelle uniquement
                if ($user->can('epargne.view.my_depots')) {
                    $builder->where('created_by', $user->id);
                }
            }
        });
    }
}