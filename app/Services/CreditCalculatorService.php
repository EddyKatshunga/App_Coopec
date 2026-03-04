<?php

namespace App\Services;

use App\Models\Credit;
use Carbon\Carbon;

class CreditCalculatorService
{
    /**
     * Ventile un remboursement selon l’ordre strict :
     * 1. pénalités
     * 2. intérêts
     * 3. capital
     */
    public function repartitionRemboursement(
        Credit $credit,
        float $montant,
        Carbon $datePaiement
    ): array {
        $reste = $montant;

        // On récupère la situation exacte à l'instant T (date du paiement)
        $situation = $credit->getSituationActuelle($datePaiement);
        
        /* ================= PÉNALITÉS ================= */
        $penalitesCourantes = $situation['penalites_courantes'];
        $penalitePayee = min($reste, $penalitesCourantes);
        $reste -= $penalitePayee;

        /* ================= INTÉRÊTS ================= */
        $interetDejaPaye = $credit->remboursements()->sum('montant_interet_payee');
        $interetRestant = max(0, $credit->interet - $interetDejaPaye);

        $interetPayee = min($reste, $interetRestant);
        $reste -= $interetPayee;

        /* ================= CAPITAL ================= */
        $capitalDejaPaye = $credit->remboursements()->sum('montant_capital_payee');
        $capitalRestant = max(0, $credit->capital - $capitalDejaPaye);

        $capitalPayee = min($reste, $capitalRestant);
        $reste -= $capitalPayee;

        return [
            'penalite_payee'      => round($penalitePayee, 5), // Précision augmentée pour les décimales de pénalités
            'interet_payee'       => round($interetPayee, 5),
            'capital_payee'       => round($capitalPayee, 5),
            'reste_non_alloue'    => round($reste, 5), // L'éventuel trop-perçu
            'reste_du_base_avant' => $situation['reste_du_base'] // Utile pour le snapshot
        ];
    }
}