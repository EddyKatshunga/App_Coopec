<?php

namespace App\Observers;

use App\Models\CreditRemboursement;
use Illuminate\Support\Facades\DB;

class CreditRemboursementObserver
{
    public function created(CreditRemboursement $model): void
    {
        $agence = $model->agence;
        $credit = $model->credit;
        $montant = $model->montant;
        $monnaie = $model->credit->monnaie;

        // Mise à jour directe du crédit (sans événements)
        DB::table('credits')
            ->where('id', $credit->id)
            ->increment('total_remboursement', $montant);
        
        // Mise à jour de l'agence
        if($monnaie === 'CDF'){
            $agence->increment('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->increment('solde_actuel_coffre_usd', $model->montant);
        }
    }

    public function deleting(CreditRemboursement $model): void
    {
        $agence = $model->agence;
        $credit = $model->credit;
        $montant = $model->montant;
        $monnaie = $model->credit->monnaie;

        DB::table('credits')
            ->where('id', $credit->id)
            ->decrement('total_remboursement', $montant);

        // Mise à jour directe de l'agence
        if($monnaie === 'CDF'){
            $agence->decrement('solde_actuel_coffre_cdf', $model->montant);
        }else{
            $agence->decrement('solde_actuel_coffre_usd', $model->montant);
        }
    }
}