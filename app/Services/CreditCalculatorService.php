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

        // 1. Récupération de la situation (Pénalités générées à cette date)
        $situation = $credit->getSituationActuelle($datePaiement);
        
        /* ================= PRIORITÉ 1 : PÉNALITÉS ================= */
        $penalitesDues = $situation['penalites_courantes'];
        $restePenalite = $reliquat < $penalitesDues ? $penalitesDues - $reliquat : 0;
        $penalitePayee = min($reliquat, $penalitesDues);
        $reliquat = max(0, $reliquat - $penalitesDues);

        /* ================= PRIORITÉ 2 : INTÉRÊTS ================= */
        // On calcule ce qu'il reste à payer en intérêts AVANT ce paiement
        $interetDejaPaye = $credit->remboursements()->sum('montant_interet_payee') ?? 0;
        $interetTotalInitial = $credit->interet;
        $interetRestantAcouvrir = max(0, $interetTotalInitial - $interetDejaPaye);

        $interetPayee = min($reliquat, $interetRestantAcouvrir);
        $reliquat -= $interetPayee;

        /* ================= PRIORITÉ 3 : CAPITAL ================= */
        // On calcule ce qu'il reste à payer en capital AVANT ce paiement
        $capitalDejaPaye = $credit->remboursements()->sum('montant_capital_payee') ?? 0;
        $capitalTotalInitial = $credit->capital;
        $capitalRestantAcouvrir = max(0, $capitalTotalInitial - $capitalDejaPaye);

        $capitalPayee = min($reliquat, $capitalRestantAcouvrir);
        $reliquat -= $capitalPayee;

        /* ================= BILAN ! ================= */
        // Le reste_non_alloue est le trop-perçu (si le client donne plus que sa dette totale)
        
        return [
            'penalite_payee'      => round($penalitePayee, 2),
            'interet_payee'       => round($interetPayee, 2),
            'capital_payee'       => round($capitalPayee, 2),
            'reste_penalite'      => round($restePenalite, 2),
            'reste_non_alloue'    => round($reliquat, 2), 
            'reste_du_base_avant' => $situation['reste_du_base']
        ];
    }
}