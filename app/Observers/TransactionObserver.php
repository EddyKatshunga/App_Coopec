<?php

namespace App\Observers;

use App\Models\JournalEntry;
use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $model): void
    {
        $this->adjustBalances($model, 'increment');
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleting(Transaction $model): void
    {
        $this->adjustBalances($model, 'decrement');
    }

    /**
     * Handle the Transaction "deleted" event.
     * Supprime l'écriture comptable liée, si elle existe.
     */
    public function deleted(Transaction $model): void
    {
        if ($model->journal_entry_id) {
            // On supprime l'écriture ; les lignes seront effacées automatiquement
            // grâce à cascadeOnDelete() sur journal_entry_lines
            JournalEntry::where('id', $model->journal_entry_id)->delete();
        }
    }

    /**
     * Centralisation de la logique pour éviter la répétition (Refactoring)
     */
    private function adjustBalances(Transaction $model, string $action): void
    {
        $compte = $model->compte;
        $m = $model->monnaie; // CDF ou USD
        
        // Déterminer si on doit inverser l'action selon le type de transaction
        // Si DEPOT : increment (normal), si RETRAIT : decrement (inverse)
        $isDepot = $model->type_transaction === 'DEPOT';
        
        $method = ($action === 'increment') 
            ? ($isDepot ? 'increment' : 'decrement') 
            : ($isDepot ? 'decrement' : 'increment');

        if ($m === 'CDF') {
            $compte->$method('solde_cdf', $model->montant);
        } else {
            $compte->$method('solde_usd', $model->montant);
        }
    }
}
