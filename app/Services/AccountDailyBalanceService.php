<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountDailyBalance;
use App\Models\CloturesComptable;
use Illuminate\Support\Facades\DB;

class AccountDailyBalanceService
{
    /**
     * Calcule et enregistre les soldes quotidiens pour une journée comptable donnée.
     *
     * @param CloturesComptable $cloture
     * @return void
     * @throws \Exception
     */
    public function computeDailyBalances(CloturesComptable $cloture)
    {
        $agenceId = $cloture->agence_id;
        $dateCloture = $cloture->date_cloture;

        // Récupérer tous les comptes actifs de l'agence (ou tous les comptes)
        $accounts = Account::where('est_actif', true)->get();

        // On traite devise par devise (CDF et USD)
        $devises = ['CDF', 'USD'];

        DB::transaction(function () use ($cloture, $agenceId, $dateCloture, $accounts, $devises) {
            foreach ($accounts as $account) {
                foreach ($devises as $devise) {
                    // 1. Solde de début = solde de fin de la veille pour (compte, agence, devise)
                    $previousBalance = AccountDailyBalance::where('account_id', $account->id)
                        ->where('agence_id', $agenceId)
                        ->where('monnaie', $devise)
                        ->whereHas('clotureComptable', function ($q) use ($dateCloture) {
                            $q->where('date_cloture', '<', $dateCloture);
                        })
                        ->orderBy('cloture_comptable_id', 'desc')
                        ->first();

                    $soldeDebut = $previousBalance ? $previousBalance->solde_fin : 0;

                    // 2. Totaux des mouvements du jour pour ce compte et cette devise
                    // On somme les débits et crédits depuis journal_entry_lines
                    // jointure avec journal_entries qui a la date_operation et agence_id + cloture_comptable_id
                    $totals = DB::table('journal_entry_lines as l')
                        ->join('journal_entries as e', 'l.journal_entry_id', '=', 'e.id')
                        ->where('e.journee_comptable_id', $cloture->id)
                        ->where('e.agence_id', $agenceId)
                        ->where('l.account_id', $account->id)
                        ->where('l.monnaie', $devise)
                        ->select(
                            DB::raw('SUM(l.debit) as total_debit'),
                            DB::raw('SUM(l.credit) as total_credit')
                        )
                        ->first();

                    $totalDebit = (float) ($totals->total_debit ?? 0);
                    $totalCredit = (float) ($totals->total_credit ?? 0);

                    // 3. Solde final
                    
                    if($account->type === 'charge' || $account->type === 'produit'){
                        $soldeFin = $soldeDebut + $totalCredit - $totalDebit;
                    }else{
                        $soldeFin = $soldeDebut + $totalDebit - $totalCredit;
                    }

                    // 4. Création ou mise à jour de la ligne uniquement si mouvements
                    if (($totalDebit != 0 || $totalCredit != 0) && ($totalDebit != $totalCredit)) {
                        AccountDailyBalance::updateOrCreate(
                            [
                                'account_id'           => $account->id,
                                'agence_id'            => $agenceId,
                                'monnaie'              => $devise,
                                'cloture_comptable_id' => $cloture->id,
                            ],
                            [
                                'solde_debut'        => $soldeDebut,
                                'total_debit_jour'   => $totalDebit,
                                'total_credit_jour'  => $totalCredit,
                                'solde_fin'          => $soldeFin,
                            ]
                        );
                    }
                }
            }
        });
    }
}