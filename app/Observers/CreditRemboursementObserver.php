<?php

namespace App\Observers;

use App\Models\CreditRemboursement;

class CreditRemboursementObserver
{
    public function created(CreditRemboursement $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }

    public function deleted(CreditRemboursement $model): void
    {
        $agence = $model->agence;
        if($model->monnaie === 'CDF'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }

    /**
     * Handle the CreditRemboursement "restored" event.
     */
    public function restored(CreditRemboursement $creditRemboursement): void
    {
        //
    }

    /**
     * Handle the CreditRemboursement "force deleted" event.
     */
    public function forceDeleted(CreditRemboursement $creditRemboursement): void
    {
        //
    }
}
