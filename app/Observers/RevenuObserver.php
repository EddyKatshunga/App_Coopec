<?php

// app/Observers/RevenuObserver.php

namespace App\Observers;

use App\Models\Revenu;

class RevenuObserver
{
    public function created(Revenu $model): void
    {
        $agence = $model->agence;

        if (!$agence) {
            throw new \Exception("L'agence associée au revenu est introuvable.");
        }
        if($model->monnaie === 'CDF'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }

    public function deleted(Revenu $model): void
    {
        $agence = $model->agence;

        if (!$agence) {
            throw new \Exception("L'agence associée au revenu est introuvable.");
        }
        if($model->monnaie === 'CDF'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }
}