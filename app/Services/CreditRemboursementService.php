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
     * - date_paiement (Optionnel, défaut = date comptable)
     * - montant
     * - agent_id
     * - mode_paiement
     * - reference_paiement
     */
    public function enregistrer(Credit $credit, array $data): CreditRemboursement
    {
        return DB::transaction(function () use ($credit, $data) {
            $montant = (float) $data['montant'];
            $zone = $credit->zone;

            // Il est crucial d'avoir la date exacte du paiement pour les pénalités
            $datePaiement = isset($data['date_paiement']) 
                ? Carbon::parse($data['date_paiement']) 
                : auth()->user()->journee_ouverte->date_cloture;

            if (!$zone) {
                throw new \InvalidArgumentException('Le crédit n\'a pas de zone.');
            }

            if ($montant <= 0) {
                throw new \InvalidArgumentException('Le montant du remboursement doit être supérieur à zéro.');
            }

            /* ================= VENTILATION ================= */
            $repartition = $this->calculator->repartitionRemboursement($credit, $montant, $datePaiement);

            // Règle d'or : Les pénalités impayées ne s'ajoutent pas au reste dû.
            // Le reste dû baisse uniquement grâce à la part du paiement allouée au capital et aux intérêts.
            $resteDuApres = max(
                0,
                $credit->reste_du - $repartition['interet_payee'] - $repartition['capital_payee']
            );

            /* ================= ENREGISTREMENT ================= */
            return CreditRemboursement::create([
                'credit_id' => $credit->id,
                'montant' => $montant,
                'monnaie' => $credit->monnaie,
                'agent_id' => $data['agent_id'],
                
                // Ventilation
                'montant_penalite_payee' => $repartition['penalite_payee'],
                'montant_interet_payee'  => $repartition['interet_payee'],
                'montant_capital_payee'  => $repartition['capital_payee'],
                'reste_penalite'  => $repartition['reste_penalite'],
                
                // Snapshots comptables
                'report_avant' => $credit->reste_du,
                'reste_du_apres' => round($resteDuApres, 5),

                'mode_paiement' => $data['mode_paiement'],
                'reference_paiement' => $data['reference_paiement'] ?? null,
                'zone_id' => $zone->id,
            ]);
        });
    }
}