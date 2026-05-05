<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Models\Transaction;
use App\Models\Credit;
use App\Models\CreditRemboursement;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountDailyBalance;
use Illuminate\Support\Facades\DB;

class ClotureVerificationService
{
    /**
     * Vérifications préalables à l'ouverture d'une nouvelle journée.
     * Retourne ['checklist' => [], 'allOk' => bool]
     */
    public function checkPreOuverture(Agence $agence): array
    {
        $checklist = [];

        // 1. Aucune journée ouverte en cours
        $journeeOuverte = CloturesComptable::where('agence_id', $agence->id)
            ->where('statut', 'ouverte')
            ->first();
        $checklist['aucune_ouverte'] = [
            'ok' => !$journeeOuverte,
            'message' => $journeeOuverte
                ? "Une journée est déjà ouverte (du {$journeeOuverte->date_cloture->format('d/m/Y')}). Veuillez la clôturer d'abord."
                : "✓ Aucune journée ouverte en cours."
        ];

        // 2. Dernière journée clôturée (si elle existe)
        $derniereJournee = CloturesComptable::where('agence_id', $agence->id)
            ->orderBy('date_cloture', 'desc')
            ->first();
        if ($derniereJournee) {
            $checklist['derniere_cloturee'] = [
                'ok' => $derniereJournee->statut === 'cloturee',
                'message' => $derniereJournee->statut === 'cloturee'
                    ? "✓ La dernière journée du {$derniereJournee->date_cloture->format('d/m/Y')} est clôturée."
                    : "⚠️ La dernière journée du {$derniereJournee->date_cloture->format('d/m/Y')} n'est pas encore clôturée."
            ];
        } else {
            $checklist['derniere_cloturee'] = [
                'ok' => true,
                'message' => "✓ Aucune journée antérieure – première ouverture."
            ];
        }

        // 3. Soldes de la veille figés (account_daily_balances)
        if ($derniereJournee && $derniereJournee->statut === 'cloturee') {
            $soldesFiges = AccountDailyBalance::where('cloture_comptable_id', $derniereJournee->id)->exists();
            $checklist['soldes_figes'] = [
                'ok' => $soldesFiges,
                'message' => $soldesFiges
                    ? "✓ Les soldes de la veille sont figés."
                    : "⚠️ Les soldes de la veille ne sont pas figés. Exécutez d'abord le calcul des soldes."
            ];
        } else {
            $checklist['soldes_figes'] = ['ok' => true, 'message' => "✓ Pas de journée précédente à figer."];
        }

        $allOk = collect($checklist)->every(fn($item) => $item['ok']);
        return compact('checklist', 'allOk');
    }

    /**
     * Vérifications avant de clôturer une journée.
     */
    public function checkPreCloture(CloturesComptable $cloture): array
    {
        $checklist = [];
        $today = $cloture->date_cloture;
        $agenceId = $cloture->agence_id;

        // 1. Statut ouvert
        $checklist['statut_ouvert'] = [
            'ok' => $cloture->statut === 'ouverte',
            'message' => $cloture->statut === 'ouverte'
                ? "✓ La journée est ouverte."
                : "❌ La journée est déjà {$cloture->statut}."
        ];

        // 2. Pas d'écriture postérieure à la date de clôture
        $ecrituresPost = JournalEntry::where('agence_id', $agenceId)
            ->where('date_operation', '>', $today)
            ->exists();
        $checklist['pas_ecritures_post'] = [
            'ok' => !$ecrituresPost,
            'message' => !$ecrituresPost
                ? "✓ Aucune écriture avec date postérieure à la clôture."
                : "❌ Des écritures existent avec une date > {$today->format('d/m/Y')}."
        ];

        // 3. Toutes les opérations du jour ont une écriture comptable associée
        $transactionsSansEcriture = Transaction::where('agence_id', $agenceId)
            ->whereDate('created_at', $today)
            ->whereNull('journal_entry_id')
            ->count();
        $creditsSansEcriture = Credit::where('agence_id', $agenceId)
            ->whereDate('created_at', $today)
            ->whereNull('journal_entry_id')
            ->count();
        $remboursementsSansEcriture = CreditRemboursement::where('agence_id', $agenceId)
            ->whereDate('created_at', $today)
            ->whereNull('journal_entry_id')
            ->count();
        $totalSansEcriture = $transactionsSansEcriture + $creditsSansEcriture + $remboursementsSansEcriture;
        $checklist['ecritures_ok'] = [
            'ok' => $totalSansEcriture === 0,
            'message' => $totalSansEcriture === 0
                ? "✓ Toutes les opérations ont leur écriture comptable."
                : "⚠️ $totalSansEcriture opération(s) sans écriture comptable (transactions: $transactionsSansEcriture, crédits: $creditsSansEcriture, remboursements: $remboursementsSansEcriture)."
        ];

        // 4. Équilibre débit = crédit par devise
        $desequilibres = $this->checkEquilibreJournaux($cloture);
        $checklist['equilibre_journaux'] = [
            'ok' => empty($desequilibres),
            'message' => empty($desequilibres)
                ? "✓ Tous les journaux sont équilibrés par devise."
                : "❌ Déséquilibres : " . implode(' ; ', $desequilibres)
        ];

        // 5. Observation (sera validée lors de la soumission, ici simple avertissement)
        $checklist['observation_ok'] = [
            'ok' => true,
            'message' => "Renseignez l'observation si nécessaire."
        ];

        $allOk = collect($checklist)->every(fn($item) => $item['ok']);
        return compact('checklist', 'allOk');
    }

    /**
     * Vérifie l'équilibre débit/crédit pour chaque devise.
     * @return array Liste des déséquilibres (ex: "USD: débit 100 ≠ crédit 95")
     */
    public function checkEquilibreJournaux(CloturesComptable $cloture): array
    {
        $desequilibres = [];
        $agenceId = $cloture->agence_id;
        $today = $cloture->date_cloture;

        foreach (['CDF', 'USD'] as $devise) {
            $totals = JournalEntryLine::whereHas('journalEntry', function ($q) use ($agenceId, $today) {
                    $q->where('agence_id', $agenceId)
                      ->whereDate('date_operation', $today);
                })
                ->where('monnaie', $devise)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            $debit = (float)($totals->total_debit ?? 0);
            $credit = (float)($totals->total_credit ?? 0);
            if (abs($debit - $credit) > 0.01) {
                $desequilibres[] = "$devise: débit $debit ≠ crédit $credit";
            }
        }
        return $desequilibres;
    }

    /**
     * Vérifie l'existence d'écritures comptables après la date de clôture.
     */
    public function hasEcrituresPosterieures(CloturesComptable $cloture): bool
    {
        return JournalEntry::where('agence_id', $cloture->agence_id)
            ->where('date_operation', '>', $cloture->date_cloture)
            ->exists();
    }
}