<?php

namespace App\Services;

use App\Models\Credit;
use Carbon\Carbon;

class CreditCalculatorService
{
    public function repartitionRemboursement(
        Credit $credit,
        float $montantTotal,
        Carbon $datePaiement
    ): array {
        // Sécurité : On travaille sur une copie du montant pour ne jamais dépasser le versement
        $reliquat = $montantTotal;

        /* ================= PRIORITÉ 1 : INTÉRÊTS ================= */
        // On calcule ce qu'il reste à payer en intérêts AVANT ce paiement
        $interetDejaPaye = $credit->remboursements()->sum('montant_interet_payee') ?? 0;
        $interetTotalInitial = $credit->interet;
        $interetRestantAcouvrir = max(0, $interetTotalInitial - $interetDejaPaye);

        $interetPayee = min($reliquat, $interetRestantAcouvrir);
        $reliquat -= $interetPayee;

        /* ================= PRIORITÉ 2 : CAPITAL ================= */
        // On calcule ce qu'il reste à payer en capital AVANT ce paiement
        $capitalDejaPaye = $credit->remboursements()->sum('montant_capital_payee') ?? 0;
        $capitalTotalInitial = $credit->capital;
        $capitalRestantAcouvrir = max(0, $capitalTotalInitial - $capitalDejaPaye);

        $capitalPayee = min($reliquat, $capitalRestantAcouvrir);
        $reliquat -= $capitalPayee;

        /* ================= BILAN ================= */
        // Le reste_non_alloue est le trop-perçu (si le client donne plus que sa dette totale)
        
        return [
            'interet_payee'    => round($interetPayee, 2),
            'capital_payee'    => round($capitalPayee, 2),
            'reste_non_alloue' => round($reliquat, 2),
        ];
    }
}