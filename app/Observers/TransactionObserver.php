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
    public function deleted(Transaction $model): void
    {
        // TRÈS IMPORTANT : Si c'est une suppression définitive, 
        // on ne fait rien car deleted() est appelé juste avant forceDeleted().
        // On évite ainsi de décompter les montants deux fois.
        if ($model->isForceDeleting()) {
            return;
        }

        $this->adjustBalances($model, 'decrement');
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $model): void
    {
        // Si on restaure une transaction, on rajoute les montants aux soldes
        $this->adjustBalances($model, 'increment');
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $model): void
    {
        // Si la transaction n'était pas déjà en soft delete au moment de l'appel
        // (donc si on forceDelete une transaction active), on ajuste les soldes.
        // Si elle était déjà en soft delete, les soldes ont déjà été ajustés par deleted().
        if (!$model->wasRecentlyCreated && $model->deleted_at === null) {
             $this->adjustBalances($model, 'decrement');
        }
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
