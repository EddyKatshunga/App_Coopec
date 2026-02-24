<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\CreditRemboursement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditRemboursementService
{
    protected CreditCalculatorService $calculator;

    public function __construct(CreditCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Enregistre un remboursement de crédit
     *
     * $data attend :
     * - date_paiement
     * - montant
     * - agent_id
     * - mode_paiement
     */
    public function enregistrer(Credit $credit, array $data): CreditRemboursement
    {
        return DB::transaction(function () use ($credit, $data) {
            $montant = (float) $data['montant'];
            $zone = $credit->zone;

            if (!$zone) {
                throw new \InvalidArgumentException('Le crédit n\'a pas de zone.');
            }

            if ($montant <= 0) {
                throw new \InvalidArgumentException('Le montant du remboursement doit être supérieur à zéro.');
            }

            /* ================= ÉTAT AVANT ================= */ 

            $totalRembourseAvant = $credit->remboursements()->sum('montant');

            $penaliteAvant = $this->calculator
                ->calculerPenalite($credit, auth()->user()->journee_ouverte->date_cloture);

            $totalDuAvant = ($credit->capital + $credit->interet)
                + $penaliteAvant
                - $totalRembourseAvant;

            /* ================= VENTILATION ================= */

            $repartition = $this->calculator
                ->repartitionRemboursement($credit, $montant);

            /* ================= ÉTAT APRÈS ================= */

            $totalRembourseApres = $totalRembourseAvant + $montant;

            $penaliteApres = max(
                0,
                $penaliteAvant - $repartition['penalite_payee']
            );

            $totalDuApres = max(
                0,
                ($credit->capital + $credit->interet)
                + $penaliteApres
                - $totalRembourseApres
            );

            /* ================= ENREGISTREMENT ================= */

            return CreditRemboursement::create([
                'credit_id' => $credit->id,
                'montant' => $montant,
                // ventilation
                'montant_penalite_payee' => $repartition['penalite_payee'],
                'montant_interet_payee'  => $repartition['interet_payee'],
                'montant_capital_payee'  => $repartition['capital_payee'],
                // snapshots comptables
                'report_avant' => round($totalRembourseAvant, 2),
                'reste_du_apres' => round($totalDuApres, 2),
                'agent_id' => $data['agent_id'],
                'mode_paiement' => $data['mode_paiement'],
                'reference_paiement' => $data['reference_paiement'],
                'zone_id' => $zone->id,
                'agence_id' => $zone->agence_id,
            ]);
        });
    }
}
