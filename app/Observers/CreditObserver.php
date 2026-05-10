<?php

namespace App\Observers;

use App\Models\Credit;
use Exception;

class CreditObserver
{
    /**
     * Avant de créer le crédit, on vérifie :
     * 1. Si le membre n'a pas déjà un crédit actif
     */
    public function creating(Credit $credit): void
    {
        /* 1. VÉRIFICATION DU CRÉDIT EN COURS */
        // On accède au membre lié au crédit
        $membre = $credit->membre;

        if ($membre && $membre->hasActiveCredit()) {
            throw new Exception("Opération impossible : Ce membre possède déjà un crédit actif (en cours ou en retard).");
        }

        /* 2. VÉRIFICATION DE L'AGENCE ET DU SOLDE */
        // On utilise la relation de l'agence définie sur le crédit ou l'utilisateur connecté
        $agence = $credit->agence ?? auth()->user()->agent?->agence; 

        if (!$agence) { 
            throw new Exception("Aucune agence associée à l'opération.");
        }
    }

    public function deleted(Credit $model): void
    {
        if ($model->journal_entry_id) {
            // On supprime l'écriture ; les lignes seront effacées automatiquement
            // grâce à cascadeOnDelete() sur journal_entry_lines
            \App\Models\JournalEntry::where('id', $model->journal_entry_id)->delete();
        }
    }
    
}