<?php

namespace App\Observers;

use App\Models\Credit;
use Exception;

class CreditObserver
{
    /**
     * Avant de créer le crédit, on vérifie :
     * 1. Si le membre n'a pas déjà un crédit actif
     * 2. Si les fonds du coffre sont suffisants
     */
    public function creating(Credit $credit): void
    {
        /* 1. VÉRIFICATION DU CRÉDIT EN COURS */
        // On accède au membre lié au crédit
        $membre = $credit->membre;

        if ($membre && $membre->creditEnCours()) {
            throw new Exception("Opération impossible : Ce membre possède déjà un crédit actif (en cours ou en retard).");
        }

        /* 2. VÉRIFICATION DE L'AGENCE ET DU SOLDE */
        // On utilise la relation de l'agence définie sur le crédit ou l'utilisateur connecté
        $agence = $credit->agence ?? auth()->user()->agent?->agence; 

        if (!$agence) { 
            throw new Exception("Aucune agence associée à l'opération."); 
        } 

        // Note : Dans votre code, vous utilisez $credit->montant, 
        // assurez-vous que cette propriété existe ou utilisez $credit->capital
        $montantA_Debiter = $credit->capital; 

        if ($credit->monnaie === 'CDF') {
            if ($agence->solde_actuel_coffre_cdf < $montantA_Debiter) {
                throw new Exception("Opagement impossible : Solde CDF du coffre insuffisant ({$agence->solde_actuel_coffre_cdf}).");
            }
        } else {
            if ($agence->solde_actuel_coffre_usd < $montantA_Debiter) {
                throw new Exception("Opération impossible : Solde USD du coffre insuffisant ({$agence->solde_actuel_coffre_usd}).");
            }
        }
    }

    public function created(Credit $model): void
    {
        $agence = $model->agence;
        // On décrémente le capital octroyé du coffre
        if ($model->monnaie === 'CDF') {
            $agence->decrement('solde_actuel_coffre_cdf', $model->capital);
        } else {
            $agence->decrement('solde_actuel_coffre_usd', $model->capital);
        }
    }

    public function deleted(Credit $model): void
    {
        $agence = $model->agence;
        if ($model->monnaie === 'CDF') {
            $agence->increment('solde_actuel_coffre_cdf', $model->capital);
        } else {
            $agence->increment('solde_actuel_coffre_usd', $model->capital);
        }
    }
    
}