<?php

namespace App\Observers;

use App\Models\Depense;
use Exception;

class DepenseObserver
{
    /**
     * Avant de créer la dépense, on vérifie si les fonds sont suffisants
     */
    public function creating(Depense $Depense): void
    {
        //On récupère l'agence ici uniquement pour vérifier les soldes disponibles
        $agence = auth()->user()->agent?->agence; 

        if (!$agence) { 
            throw new Exception("Aucune agence associée à l'utilisateur."); 
        } 

        if($Depense->monnaie === 'CDF'){
            if ($agence->solde_actuel_coffre_cdf < $Depense->montant) {
                throw new Exception("Opération impossible : Solde CDF du coffre insuffisant ----- ({$agence->solde_actuel_coffre_cdf}).");
            }
        }else{
            if ($agence->solde_actuel_coffre_usd < $Depense->montant) {
                throw new Exception("Opération impossible : Solde USD du coffre insuffisant ----- ({$agence->solde_actuel_coffre_usd}).");
            }
        }
    }

    public function created(Depense $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }

    public function deleted(Depense $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }
}
