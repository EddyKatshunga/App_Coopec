<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\TauxChange;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccountingService
{

    public function record(array $lines, string $libelle, $source = null)
    {
        return DB::transaction(function () use ($lines, $libelle, $source) {
            $tauxChangeActif = TauxChange::actuel();
            $tauxVente = $tauxChangeActif?->taux_vente;

            $linesEnrichies = collect($lines)->map(function ($line) use ($tauxVente) {
                if (($line['monnaie'] ?? 'CDF') === 'USD' && !isset($line['taux_change'])) {
                    $line['taux_change'] = $tauxVente;
                }
                return $line;
            });

            // Calcul des montants en devise de référence (CDF)
            $totalDebitBase = $linesEnrichies->sum(function ($line) {
                $montant = $line['debit'] ?? 0;
                if ($montant == 0) return 0;
                $taux = ($line['monnaie'] ?? 'CDF') === 'USD' ? ($line['taux_change'] ?? 1) : 1;
                return $montant * $taux;
            });

            $totalCreditBase = $linesEnrichies->sum(function ($line) {
                $montant = $line['credit'] ?? 0;
                if ($montant == 0) return 0;
                $taux = ($line['monnaie'] ?? 'CDF') === 'USD' ? ($line['taux_change'] ?? 1) : 1;
                return $montant * $taux;
            });

            if (round($totalDebitBase, 2) !== round($totalCreditBase, 2)) {
                throw new Exception("Écriture non équilibrée en CDF : DébitBase ($totalDebitBase) ≠ CréditBase ($totalCreditBase)");
            }

            $agenceId = Auth::user()->agence_id;
            $journee = Auth::user()->journee_ouverte;
            if (!$journee) {
                throw new Exception("Aucune journée comptable ouverte pour aujourd'hui.");
            }

            $entry = JournalEntry::create([
                'uuid' => (string) Str::uuid(),
                'libelle' => $libelle,
                'date_operation' => $journee->date_cloture,
                'agence_id' => $agenceId,
                'journee_comptable_id' => $journee->id,
            ]);

            foreach ($linesEnrichies as $line) {
                $montant = $line['debit'] > 0 ? $line['debit'] : $line['credit'];
                $montantBase = ($line['monnaie'] ?? 'CDF') === 'USD' && isset($line['taux_change']) && $line['taux_change'] > 0
                    ? $montant * $line['taux_change']
                    : $montant;

                $entry->lines()->create([
                    'account_id'   => $line['account_id'],
                    'debit'        => $line['debit'] ?? 0,
                    'credit'       => $line['credit'] ?? 0,
                    'monnaie'      => $line['monnaie'] ?? 'CDF',
                    'taux_change'  => $line['taux_change'] ?? null,
                    'montant_base' => round($montantBase, 2),
                ]);
            }

            if ($source) {
                $source->journal_entry_id = $entry->id;
            }

            return $entry;
        });
    }
}