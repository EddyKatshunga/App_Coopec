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
        $agence = $model->agence;
        
        // LOGIQUE CORRIGÉE : 
        // Seuls le capital et l'intérêt payés diminuent la dette (total_remboursement)
        $partDette = (float) ($model->montant_capital_payee + $model->montant_interet_payee);
        // Le montant total (avec pénalités) entre dans le coffre
        $montantGlobal = (float) $model->montant;

        DB::transaction(function () use ($credit, $agence, $partDette, $montantGlobal, $model) {
            
            // 1. Mise à jour de la dette du Crédit
            if ($partDette > 0) {
                DB::table('credits')
                    ->where('id', $credit->id)
                    ->increment('total_remboursement', $partDette);
            }

            // 2. Mise à jour du coffre de l'Agence (Flux financier total)
            $colonneCoffre = ($model->monnaie === 'CDF') ? 'solde_actuel_coffre_cdf' : 'solde_actuel_coffre_usd';
            
            DB::table('agences')
                ->where('id', $agence->id)
                ->increment($colonneCoffre, $montantGlobal);
        });
    }

    public function deleting(CreditRemboursement $model): void
    {
        $credit = $model->credit;
        $agence = $model->agence;

        $partDette = (float) ($model->montant_capital_payee + $model->montant_interet_payee);
        $montantGlobal = (float) $model->montant;

        DB::transaction(function () use ($credit, $agence, $partDette, $montantGlobal, $model) {
            
            // 1. Restauration de la dette
            if ($partDette > 0) {
                DB::table('credits')
                    ->where('id', $credit->id)
                    ->decrement('total_remboursement', $partDette);
            }

            // 2. Retrait du coffre de l'Agence
            $colonneCoffre = ($model->monnaie === 'CDF') ? 'solde_actuel_coffre_cdf' : 'solde_actuel_coffre_usd';
            
            DB::table('agences')
                ->where('id', $agence->id)
                ->decrement($colonneCoffre, $montantGlobal);
        });
    }
}