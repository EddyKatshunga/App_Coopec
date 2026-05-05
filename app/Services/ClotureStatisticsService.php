<?php

namespace App\Services;

use App\Models\CloturesComptable;
use App\Models\Transaction;
use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;

class ClotureStatisticsService
{
    /**
     * Retourne les statistiques complètes pour une clôture donnée.
     */
    public function getStatistics(CloturesComptable $cloture): array
    {
        $today = $cloture->date_cloture;
        $agenceId = $cloture->agence_id;

        // Nombre d'écritures comptables du jour
        $nbEcritures = JournalEntry::where('agence_id', $agenceId)
            ->whereDate('date_operation', $today)
            ->count();

        // Totaux débit/crédit par devise
        $statsDevises = [];
        foreach (['CDF', 'USD'] as $devise) {
            $totals = JournalEntryLine::whereHas('journalEntry', function ($q) use ($today, $agenceId) {
                    $q->where('agence_id', $agenceId)->whereDate('date_operation', $today);
                })
                ->where('monnaie', $devise)
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();
            $statsDevises[$devise] = [
                'debit'  => (float)($totals->debit ?? 0),
                'credit' => (float)($totals->credit ?? 0),
            ];
        }

        // Opérations par type
        $depotsUSD = Transaction::where('agence_id', $agenceId)
            ->whereDate('date_transaction', $today)
            ->where('type_transaction', 'DEPOT')
            ->where('monnaie', 'USD')
            ->sum('montant');
        $depotsCDF = Transaction::where('agence_id', $agenceId)
            ->whereDate('date_transaction', $today)
            ->where('type_transaction', 'DEPOT')
            ->where('monnaie', 'CDF')
            ->sum('montant');
        $retraitsUSD = Transaction::where('agence_id', $agenceId)
            ->whereDate('date_transaction', $today)
            ->where('type_transaction', 'RETRAIT')
            ->where('monnaie', 'USD')
            ->sum('montant');
        $retraitsCDF = Transaction::where('agence_id', $agenceId)
            ->whereDate('date_transaction', $today)
            ->where('type_transaction', 'RETRAIT')
            ->where('monnaie', 'CDF')
            ->sum('montant');

        $creditsUSD = Credit::where('agence_id', $agenceId)
            ->whereDate('date_credit', $today)
            ->where('monnaie', 'USD')
            ->sum('capital');
        $creditsCDF = Credit::where('agence_id', $agenceId)
            ->whereDate('date_credit', $today)
            ->where('monnaie', 'CDF')
            ->sum('capital');

        $remboursementsUSD = CreditRemboursement::where('agence_id', $agenceId)
            ->whereDate('date_paiement', $today)
            ->where('monnaie', 'USD')
            ->sum('montant');
        $remboursementsCDF = CreditRemboursement::where('agence_id', $agenceId)
            ->whereDate('date_paiement', $today)
            ->where('monnaie', 'CDF')
            ->sum('montant');

        return [
            'nb_ecritures' => $nbEcritures,
            'stats_devises' => $statsDevises,
            'depots' => [
                'USD' => $depotsUSD,
                'CDF' => $depotsCDF,
            ],
            'retraits' => [
                'USD' => $retraitsUSD,
                'CDF' => $retraitsCDF,
            ],
            'credits' => [
                'USD' => $creditsUSD,
                'CDF' => $creditsCDF,
            ],
            'remboursements' => [
                'USD' => $remboursementsUSD,
                'CDF' => $remboursementsCDF,
            ],
        ];
    }
}