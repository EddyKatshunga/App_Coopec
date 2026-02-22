<?php

namespace App\Observers;

use App\Models\Credit;

class CreditObserver
{
    public function created(Credit $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }

    public function deleted(Credit $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }

    /**
     * Handle the Credit "updated" event.
     */
    public function updated(Credit $credit): void
    {
        //
    }

    /**
     * Handle the Credit "restored" event.
     */
    public function restored(Credit $credit): void
    {
        //
    }

    /**
     * Handle the Credit "force deleted" event.
     */
    public function forceDeleted(Credit $credit): void
    {
        //
    }
}
