<?php

namespace App\Services;

use App\Models\Compte;
use Carbon\Carbon;

class CompteReleveService
{
    protected Compte $compte;

    public function __construct(Compte $compte)
    {
        $this->compte = $compte;
    }

    /**
     * Prépare les données du relevé pour une période donnée
     */
    public function getReleveData(Carbon $dateDebut, Carbon $dateFin): array
    {
        // Soldes avant la période (report)
        $transactions = $this->compte->transactions()
            ->where('date_transaction', '<', $dateDebut)
            ->get(); // <--- Important, récupère la collection

        $reportCDF = $transactions
            ->where('monnaie', 'CDF')
            ->sum(function($t) {
                return $t->type_transaction === 'DEPOT' ? $t->montant : -$t->montant;
            });

        $reportUSD = $transactions
            ->where('monnaie', 'USD')
            ->sum(function($t) {
                return $t->type_transaction === 'DEPOT' ? $t->montant : -$t->montant;
            });
    
    // Totaux dépôts/retraits pendant la période
        $totalDepotCDF = $transactions->where('monnaie', 'CDF')->where('type_transaction', 'DEPOT')->sum('montant');
        $totalRetraitCDF = $transactions->where('monnaie', 'CDF')->where('type_transaction', 'RETRAIT')->sum('montant');
        $totalDepotUSD = $transactions->where('monnaie', 'USD')->where('type_transaction', 'DEPOT')->sum('montant');
        $totalRetraitUSD = $transactions->where('monnaie', 'USD')->where('type_transaction', 'RETRAIT')->sum('montant');

        // Soldes fin de période
        $soldeCDF = $reportCDF + $totalDepotCDF - $totalRetraitCDF;
        $soldeUSD = $reportUSD + $totalDepotUSD - $totalRetraitUSD;

        return [
            'compte' => $this->compte,
            'membre' => $this->compte->membre,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'transactions' => $transactions,
            'reportCDF' => $reportCDF,
            'reportUSD' => $reportUSD,
            'totalDepotCDF' => $totalDepotCDF,
            'totalRetraitCDF' => $totalRetraitCDF,
            'totalDepotUSD' => $totalDepotUSD,
            'totalRetraitUSD' => $totalRetraitUSD,
            'soldeCDF' => $soldeCDF,
            'soldeUSD' => $soldeUSD,
        ];
    }
}
