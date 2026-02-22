<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF' && $model->type === 'DEPOT'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }elseif($model->monnaie === 'USD' && $model->type === 'DEPOT'){
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }elseif($model->monnaie === 'CDF' && $model->type === 'RETRAIT'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }elseif($model->monnaie === 'USD' && $model->type === 'RETRAIT'){
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF' && $model->type === 'DEPOT'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }elseif($model->monnaie === 'USD' && $model->type === 'DEPOT'){
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }elseif($model->monnaie === 'CDF' && $model->type === 'RETRAIT'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }elseif($model->monnaie === 'USD' && $model->type === 'RETRAIT'){
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
