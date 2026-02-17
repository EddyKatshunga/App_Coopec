<?php

// app/Observers/RevenuObserver.php

namespace App\Observers;

use App\Models\Revenu;

class RevenuObserver
{
    public function created(Revenu $revenu): void
    {
        $agence = $revenu->agence;
        $agence->increment('solde_actuel_coffre', $revenu->montant);
    }

    public function deleted(Revenu $revenu): void
    {
        $agence = $revenu->agence;
        $agence->decrement('solde_actuel_coffre', $revenu->montant);
    }
}