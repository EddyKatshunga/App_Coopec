<?php

namespace App\Observers;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CoffreObserver
{
    public function creating(Model $model): void
    {
        $agence = $model->agence;
        $montant = $model->getAmountValue();
        $column = $model->monnaie === 'CDF' ? 'solde_actuel_coffre_cdf' : 'solde_actuel_coffre_usd';

        // Si c'est une sortie d'argent, on vérifie le solde
        if (!$model->isAddition()) {
            if ($agence->$column < $montant) {
                throw new Exception("Opération impossible : Solde {$model->monnaie} insuffisant ({$agence->$column}).");
            }
        }
    }

    public function created(Model $model): void
    {
        $this->updateSolde($model, 'apply');
    }

    public function deleted(Model $model): void
    {
        $this->updateSolde($model, 'reverse');
    }

    protected function updateSolde(Model $model, string $action): void
    {
        $agence = $model->agence;
        $montant = $model->getAmountValue();
        $isAddition = $model->isAddition();
        $column = $model->monnaie === 'CDF' ? 'solde_actuel_coffre_cdf' : 'solde_actuel_coffre_usd';

        // Logique d'inversion si on supprime l'enregistrement
        if ($action === 'reverse') {
            $isAddition = !$isAddition;
        }

        if ($isAddition) {
            $agence->increment($column, $montant);
        } else {
            $agence->decrement($column, $montant);
        }
    }
}