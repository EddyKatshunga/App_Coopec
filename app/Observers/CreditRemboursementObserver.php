<?php

namespace App\Observers;

use App\Models\CreditRemboursement;
use Exception;
use Illuminate\Support\Facades\DB;

class CreditRemboursementObserver
{
    public function creating(CreditRemboursement $model): void
    {
        $credit = $model->credit;
        // Empêcher tout nouveau remboursement si le crédit est clôturé
        if (str_contains($credit->statut, 'termine')) {
            throw new Exception("Opération impossible : Le crédit est déjà clôturé ou terminé.");
        }
    }

    public function created(CreditRemboursement $model): void
    {
        $credit = $model->credit;
        
        // LOGIQUE CORRIGÉE : 
        // Seuls le capital et l'intérêt payés diminuent la dette (total_remboursement)
        $partDette = (float) ($model->montant_capital_payee + $model->montant_interet_payee);

        DB::transaction(function () use ($credit, $partDette, $model) {
            
            // 1. Mise à jour de la dette du Crédit
            if ($partDette > 0) {
                DB::table('credits')
                    ->where('id', $credit->id)
                    ->increment('total_remboursement', $partDette);
            }

        });
    }

    public function deleted(CreditRemboursement $model): void
    {
        $credit = $model->credit;

        $partDette = (float) ($model->montant_capital_payee + $model->montant_interet_payee);

        DB::transaction(function () use ($credit, $partDette, $model) {
            
            // 1. Restauration de la dette
            if ($partDette > 0) {
                DB::table('credits')
                    ->where('id', $credit->id)
                    ->decrement('total_remboursement', $partDette);
            }
        });

        if ($model->journal_entry_id) {
            // On supprime l'écriture ; les lignes seront effacées automatiquement
            // grâce à cascadeOnDelete() sur journal_entry_lines
            \App\Models\JournalEntry::where('id', $model->journal_entry_id)->delete();
        }
    }

}