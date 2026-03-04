<?php

namespace App\Observers;

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
     * Centralisation de la logique pour éviter la répétition (Refactoring)
     */
    private function adjustBalances(Transaction $model, string $action): void
    {
        $agence = $model->agence;
        $compte = $model->compte;
        $m = $model->monnaie; // CDF ou USD
        
        // Déterminer si on doit inverser l'action selon le type de transaction
        // Si DEPOT : increment (normal), si RETRAIT : decrement (inverse)
        $isDepot = $model->type_transaction === 'DEPOT';
        
        $method = ($action === 'increment') 
            ? ($isDepot ? 'increment' : 'decrement') 
            : ($isDepot ? 'decrement' : 'increment');

        if ($m === 'CDF') {
            $agence->$method('solde_actuel_coffre_cdf', $model->montant);
            $agence->$method('solde_actuel_epargne_cdf', $model->montant);
            $compte->$method('solde_cdf', $model->montant);
        } else {
            $agence->$method('solde_actuel_coffre_usd', $model->montant);
            $agence->$method('solde_actuel_epargne_usd', $model->montant);
            $compte->$method('solde_usd', $model->montant);
        }
    }
}
