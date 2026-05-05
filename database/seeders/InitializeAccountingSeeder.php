<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Models\AccountDailyBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitializeAccountingSeeder extends Seeder
{
    /**
     * Exécute le seeding : comptes, journée comptable, soldes initiaux, clôture.
     */
    public function run(): void
    {
        // 1. S'assurer qu'il existe au moins une agence (id = 1 par défaut)
        $agence = Agence::first();

        // 3. Définir la date d'ouverture comptable (par exemple, la veille ou une date fixe)
        $dateOuverture = now()->subDay()->format('Y-m-d');

        // 4. Créer la journée comptable avec statut 'ouverte'
        $cloture = CloturesComptable::updateOrCreate(
            [
                'agence_id' => $agence->id,
                'date_cloture' => $dateOuverture,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'statut' => 'ouverte',
                'created_by' => 1,
            ]
        );

        // 5. Définir les soldes initiaux pour chaque compte et devise
        //    On récupère tous les comptes actifs
        $accounts = Account::where('est_actif', true)->get();
        $devises = ['CDF', 'USD'];

        // Tableau personnalisable des soldes de début (par compte et devise)
        // Si un compte n'est pas listé, son solde de début sera 0
        $soldesInitiaux = [
            // Comptes de trésorerie
            '57' => [ // Caisse
                'CDF' => \App\Models\Compte::sum('solde_cdf'),
                'USD' => \App\Models\Compte::sum('solde_usd'),
            ],
            // Épargne membres
            '41' => [
                'CDF' => \App\Models\Compte::sum('solde_cdf'),
                'USD' => \App\Models\Compte::sum('solde_usd'),
            ],
        ];

        DB::transaction(function () use ($accounts, $devises, $cloture, $agence, $soldesInitiaux) {
            foreach ($accounts as $account) {
                foreach ($devises as $devise) {
                    // Récupérer le solde de début depuis le tableau personnalisé
                    $soldeDebut = $soldesInitiaux[$account->numero][$devise] ?? 0;

                    // Pour une journée d'initialisation, on considère qu'il n'y a aucun mouvement du jour
                    $totalDebit = 0;
                    $totalCredit = 0;

                    // Calcul du solde final selon le type de compte (même logique que dans le service)
                    if ($account->type === 'charge' || $account->type === 'produit') {
                        $soldeFin = $soldeDebut + $totalCredit - $totalDebit;
                    } else {
                        $soldeFin = $soldeDebut + $totalDebit - $totalCredit;
                    }

                    // Création de la ligne dans AccountDailyBalance
                    AccountDailyBalance::updateOrCreate(
                        [
                            'account_id'           => $account->id,
                            'agence_id'            => $agence->id,
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
        });

        // 6. Clôturer la journée (passer le statut à 'cloturee')
        $cloture->update(['statut' => 'cloturee']);

        $this->command->info('✅ Initialisation comptable terminée : journée du ' . $dateOuverture . ' clôturée avec soldes initiaux.');
    }
}