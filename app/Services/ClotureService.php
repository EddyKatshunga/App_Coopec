<?php

namespace App\Services;

use App\Models\CloturesComptable;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;

class ClotureService
{
    /**
     * Ouvre une nouvelle journée comptable pour une agence
     */
    public function ouvrirJournee(int $agenceId): CloturesComptable
    {
        return DB::transaction(function () use ($agenceId) {
            if (!$agenceId) {
                throw new Exception("L'Agence est Obligatoire.");
            }

            $dejaOuverte = CloturesComptable::where('agence_id', $agenceId)
                ->where('statut', 'ouverte')
                ->exists();

            if ($dejaOuverte) {
                throw new Exception("Une journée est déjà ouverte pour cette agence.");
            }

            $derniere = CloturesComptable::where('agence_id', $agenceId)
                ->orderBy('id', 'desc')
                ->first();

            return CloturesComptable::create([
                'agence_id'         => $agenceId,
                'date_cloture'      => Carbon::create(2025, 12, 15), // production: now()->format('Y-m-d')
                'statut'            => 'ouverte',
                'report_coffre_usd' => $derniere->solde_coffre_usd ?? 0,
                'report_coffre_cdf' => $derniere->solde_coffre_cdf ?? 0,
                'report_epargne_usd' => $derniere->solde_epargne_usd ?? 0,
                'report_epargne_cdf' => $derniere->solde_epargne_cdf ?? 0,
            ]);
        });
    }

    /**
     * Clôture définitivement la journée
     */
    public function cloturerJournee(CloturesComptable $cloture, array $donneesPhysiques): bool
    {
        if ($cloture->statut === 'cloturee') {
            throw new Exception("Cette journée est déjà clôturée.");
        }

        // 1. Calculer tous les totaux en mémoire
        $this->calculerTotaux($cloture);

        // 2. Assigner les données de clôture finale
        $cloture->physique_coffre_usd = $donneesPhysiques['usd'] ?? 0;
        $cloture->physique_coffre_cdf = $donneesPhysiques['cdf'] ?? 0;
        $cloture->observation_cloture = $donneesPhysiques['observation'] ?? null;
        $cloture->statut = 'cloturee';

        // 3. Sauvegarde finale unique (Persiste toutes les modifs du fill + les modifs ci-dessus)
        return $cloture->save();
    }

    /**
     * Recalcule tous les totaux
     */
    private function calculerTotaux(CloturesComptable $cloture): void
    {
        // On récupère les sommes avec un fallback à 0 pour éviter les erreurs de calcul
        $data = [
            'total_revenu_usd'        => $cloture->revenus()->where('monnaie', 'USD')->sum('montant') ?? 0,
            'total_revenu_cdf'        => $cloture->revenus()->where('monnaie', 'CDF')->sum('montant') ?? 0,
            'total_depense_usd'       => $cloture->depenses()->where('monnaie', 'USD')->sum('montant') ?? 0,
            'total_depense_cdf'       => $cloture->depenses()->where('monnaie', 'CDF')->sum('montant') ?? 0,
            'total_credit_usd'        => $cloture->credits()->where('monnaie', 'USD')->sum('capital') ?? 0,
            'total_credit_cdf'        => $cloture->credits()->where('monnaie', 'CDF')->sum('capital') ?? 0,
            'total_interet_generes_usd' => $cloture->credits()->where('monnaie', 'USD')->sum('interet') ?? 0,
            'total_interet_generes_cdf' => $cloture->credits()->where('monnaie', 'CDF')->sum('interet') ?? 0,
            'total_remboursement_usd' => $cloture->remboursements()->where('monnaie', 'USD')->sum('montant') ?? 0,
            'total_remboursement_cdf' => $cloture->remboursements()->where('monnaie', 'CDF')->sum('montant') ?? 0,
            'total_depot_usd'         => $cloture->depots()->where('monnaie', 'USD')->sum('montant') ?? 0,
            'total_depot_cdf'         => $cloture->depots()->where('monnaie', 'CDF')->sum('montant') ?? 0,
            'total_retrait_usd'       => $cloture->retraits()->where('monnaie', 'USD')->sum('montant') ?? 0,
            'total_retrait_cdf'       => $cloture->retraits()->where('monnaie', 'CDF')->sum('montant') ?? 0,
        ];

        // Remplissage de l'objet
        $cloture->fill($data);

        // Calcul des soldes (Théoriques en caisse)
        $cloture->solde_coffre_usd = $this->calculerSoldeFinal($cloture, 'usd');
        $cloture->solde_coffre_cdf = $this->calculerSoldeFinal($cloture, 'cdf');
        
        // Calcul du solde épargne global
        $cloture->solde_epargne_usd = ($cloture->report_epargne_usd ?? 0) + $data['total_depot_usd'] - $data['total_retrait_usd'];
        $cloture->solde_epargne_cdf = ($cloture->report_epargne_cdf ?? 0) + $data['total_depot_cdf'] - $data['total_retrait_cdf'];
    }

    private function calculerSoldeFinal(CloturesComptable $cloture, string $devise): float
    {
        $report  = (float) ($devise === 'usd' ? $cloture->report_coffre_usd : $cloture->report_coffre_cdf);
        $entrees = (float) ($cloture->{"total_depot_$devise"} + $cloture->{"total_remboursement_$devise"} + $cloture->{"total_revenu_$devise"});
        $sorties = (float) ($cloture->{"total_retrait_$devise"} + $cloture->{"total_credit_$devise"} + $cloture->{"total_depense_$devise"});

        return ($report + $entrees) - $sorties;
    }
}