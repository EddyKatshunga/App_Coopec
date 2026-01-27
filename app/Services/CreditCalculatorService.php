<?php

namespace App\Services;

use App\Models\Credit;
use Carbon\Carbon;

class CreditCalculatorService
{
    /**
     * Calcule la pénalité cumulée à une date donnée
     * (sans tenir compte d’un paiement effectué le même jour)
     */
    public function calculerPenalite(Credit $credit, Carbon $date): float
    {
        // Si négocié ou clôturé de force → plus de pénalités
        if ($credit->negocie || $credit->date_cloture_forcee) {
            return 0;
        }

        // J+11 après la date de fin prévue
        $dateDebutPenalite = $credit->date_fin_prevue->copy()->addDays(10);

        if ($date->lte($dateDebutPenalite)) {
            return 0;
        }

        $joursRetard = $dateDebutPenalite->diffInDays($date);

        // Capital encore exposé aux pénalités
        $capitalRembourse = $credit->remboursements()
            ->sum('montant_capital_payee');

        $capitalPenalisable = max(
            0,
            $credit->capital - $capitalRembourse
        );

        if ($capitalPenalisable <= 0) {
            return 0;
        }

        // pénalité = capital restant × taux journalier × jours
        return round(
            ($capitalPenalisable * $credit->taux_penalite_journalier / 100)
            * $joursRetard,
            2
        );
    }

    /**
     * Ventile un remboursement selon l’ordre :
     * 1. pénalités
     * 2. intérêts
     * 3. capital
     */
    public function repartitionRemboursement(
        Credit $credit,
        float $montant
    ): array {
        $reste = $montant;

        /* ================= PÉNALITÉS ================= */
        $penalitesCourantes = $this->calculerPenalite($credit, now());

        $penalitePayee = min($reste, $penalitesCourantes);
        $reste -= $penalitePayee;

        /* ================= INTÉRÊTS ================= */
        $interetDejaPaye = $credit->remboursements()
            ->sum('montant_interet_payee');

        $interetRestant = max(
            0,
            $credit->interet - $interetDejaPaye
        );

        $interetPayee = min($reste, $interetRestant);
        $reste -= $interetPayee;

        /* ================= CAPITAL ================= */
        $capitalDejaPaye = $credit->remboursements()
            ->sum('montant_capital_payee');

        $capitalRestant = max(
            0,
            $credit->capital - $capitalDejaPaye
        );

        $capitalPayee = min($reste, $capitalRestant);
        $reste -= $capitalPayee;

        return [
            'penalite_payee' => round($penalitePayee, 2),
            'interet_payee'  => round($interetPayee, 2),
            'capital_payee'  => round($capitalPayee, 2),
            'reste'          => round($reste, 2),
        ];
    }
}
