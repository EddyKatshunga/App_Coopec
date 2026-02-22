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
            if(!$agenceId){
                throw new Exception("L'Agence est Obligatoire.");
            }
            $dejaOuverte = CloturesComptable::where('agence_id', $agenceId)
                ->where('statut', 'ouverte')
                ->exists();

            if ($dejaOuverte) {
                throw new Exception("Une journée est déjà ouverte pour cette agence.");
            }

            $derniere = CloturesComptable::where('agence_id', $agenceId)
                ->orderBy('date_cloture', 'desc')
                ->first();

            return CloturesComptable::create([
                'agence_id'         => $agenceId,
                'date_cloture'      => Carbon::create(2025, 12, 15), //production: now()->format('Y-m-d')
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

        $this->calculerTotaux($cloture);

        return $cloture->update([
            'physique_coffre_usd' => $donneesPhysiques['usd'],
            'physique_coffre_cdf' => $donneesPhysiques['cdf'],
            'observation_cloture' => $donneesPhysiques['observation'] ?? null,
            'statut'              => 'cloturee',
        ]);
    }

    /**
     * Recalcule tous les totaux en utilisant les relations Eloquent
     * pour garantir le respect des clés étrangères, des soft deletes et des statuts.
     */
    private function calculerTotaux(CloturesComptable $cloture): void
    {
        // Revenus
        $revenuUsd = $cloture->revenus()->where('monnaie', 'USD')->sum('montant');
        $revenuCdf = $cloture->revenus()->where('monnaie', 'CDF')->sum('montant');

        // Dépenses
        $depenseUsd = $cloture->depenses()->where('monnaie', 'USD')->sum('montant');
        $depenseCdf = $cloture->depenses()->where('monnaie', 'CDF')->sum('montant');

        // Crédits octroyés (Sorties)
        $creditUsd = $cloture->credits()->where('monnaie', 'USD')->sum('montant');
        $creditCdf = $cloture->credits()->where('monnaie', 'CDF')->sum('montant');

        // Remboursements de crédits (Entrées)
        $remboursementUsd = $cloture->remboursements()->where('monnaie', 'USD')->sum('montant');
        $remboursementCdf = $cloture->remboursements()->where('monnaie', 'CDF')->sum('montant');

        // Dépôts d'épargne (Entrées)
        $depotUsd = $cloture->depots()->where('monnaie', 'USD')->sum('montant');
        $depotCdf = $cloture->depots()->where('monnaie', 'CDF')->sum('montant');

        // Retraits d'épargne (Sorties)
        $retraitUsd = $cloture->retraits()->where('monnaie', 'USD')->sum('montant');
        $retraitCdf = $cloture->retraits()->where('monnaie', 'CDF')->sum('montant');


        // 2. Mise à jour des champs de la clôture
        $cloture->fill([
            'total_revenu_usd'        => $revenuUsd,
            'total_revenu_cdf'        => $revenuCdf,
            'total_depense_usd'       => $depenseUsd,
            'total_depense_cdf'       => $depenseCdf,
            'total_depot_usd'         => $depotUsd,
            'total_depot_cdf'         => $depotCdf,
            'total_retrait_usd'       => $retraitUsd,
            'total_retrait_cdf'       => $retraitCdf,
            'total_credit_usd'        => $creditUsd,
            'total_credit_cdf'        => $creditCdf,
            'total_remboursement_usd' => $remboursementUsd,
            'total_remboursement_cdf' => $remboursementCdf,
        ]);

        // 3. Calcul des soldes finaux (Théoriques en caisse)
        $cloture->solde_coffre_usd = $this->calculerSoldeFinal($cloture, 'usd');
        $cloture->solde_coffre_cdf = $this->calculerSoldeFinal($cloture, 'cdf');
        
        // 4. Calcul du solde épargne (Stock global de l'épargne) : Report + Dépôts - Retraits
        $cloture->solde_epargne_usd = $cloture->report_epargne_usd + $depotUsd - $retraitUsd;
        $cloture->solde_epargne_cdf = $cloture->report_epargne_cdf + $depotCdf - $retraitCdf;

        // 5. Sauvegarde en base de données
        $cloture->save();
    }

    private function calculerSoldeFinal(CloturesComptable $cloture, string $devise): float
    {
        $report = ($devise === 'usd') ? $cloture->report_coffre_usd : $cloture->report_coffre_cdf;
        
        $entrees = $cloture->{"total_depot_$devise"} 
                 + $cloture->{"total_remboursement_$devise"} 
                 + $cloture->{"total_revenu_$devise"};

        $sorties = $cloture->{"total_retrait_$devise"} 
                 + $cloture->{"total_credit_$devise"} 
                 + $cloture->{"total_depense_$devise"};

        return ($report + $entrees) - $sorties;
    }
}