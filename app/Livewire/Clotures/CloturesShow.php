<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use App\Models\Account;
use App\Models\AccountDailyBalance;
use App\Services\ClotureStatisticsService;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CloturesShow extends Component
{
    public CloturesComptable $cloture;

    // Indicateurs dashboard
    public array $soldesCaisse = [];
    public array $stats = [];

    protected ClotureStatisticsService $statisticsService;

    public function boot(ClotureStatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function mount(CloturesComptable $cloture)
    {
        $this->authorize('view', $cloture);
        $this->cloture = $cloture;
        $this->loadDashboardData();
    }

    /**
     * Charger les données du tableau de bord
     */
    protected function loadDashboardData(): void
    {
        $agenceId = $this->cloture->agence_id;
        $dateCloture = $this->cloture->date_cloture->toDateString();

        // 1. Soldes de caisse (compte 57)
        $compteCaisse = Account::where('numero', '57')->first();
        if ($compteCaisse) {
            if ($this->cloture->statut === 'cloturee') {
                $soldeCdf = AccountDailyBalance::where('account_id', $compteCaisse->id)
                    ->where('agence_id', $agenceId)
                    ->where('monnaie', 'CDF')
                    ->where('cloture_comptable_id', $this->cloture->id)
                    ->value('solde_fin') ?? 0;
                $soldeUsd = AccountDailyBalance::where('account_id', $compteCaisse->id)
                    ->where('agence_id', $agenceId)
                    ->where('monnaie', 'USD')
                    ->where('cloture_comptable_id', $this->cloture->id)
                    ->value('solde_fin') ?? 0;
            } else {
                // Journée ouverte : calcul dynamique
                $soldeCdf = $this->getDynamicBalance($compteCaisse->id, $agenceId, 'CDF');
                $soldeUsd = $this->getDynamicBalance($compteCaisse->id, $agenceId, 'USD');
            }
            $this->soldesCaisse = [
                'CDF' => $soldeCdf,
                'USD' => $soldeUsd,
            ];
        }

        // 2. Statistiques opérationnelles via ClotureStatisticsService
        $statsData = $this->statisticsService->getStatistics($this->cloture);

        $this->stats = [
            // Compteurs d'opérations (relations directes, toujours disponibles)
            'nb_transactions'      => $this->cloture->transactions()->count(),
            'nb_credits'           => $this->cloture->credits()->count(),
            'nb_remboursements'    => $this->cloture->remboursements()->count(),

            // Montants agrégés issus du service
            'total_depot_cdf'      => $statsData['depots']['CDF'],
            'total_depot_usd'      => $statsData['depots']['USD'],
            'total_retrait_cdf'    => $statsData['retraits']['CDF'],
            'total_retrait_usd'    => $statsData['retraits']['USD'],
            'total_credit_cdf'     => $statsData['credits']['CDF'],
            'total_credit_usd'     => $statsData['credits']['USD'],
            'total_remboursement_cdf' => $statsData['remboursements']['CDF'],
            'total_remboursement_usd' => $statsData['remboursements']['USD'],
        ];
    }

    /**
     * Calcule le solde dynamique d’un compte pour une journée non clôturée
     * (basé sur le dernier solde figé + mouvements du jour)
     */
    protected function getDynamicBalance(int $accountId, int $agenceId, string $devise): float
    {
        // Dernier solde figé avant cette journée
        $lastClosed = CloturesComptable::where('agence_id', $agenceId)
            ->where('date_cloture', '<', $this->cloture->date_cloture)
            ->where('statut', 'cloturee')
            ->orderBy('date_cloture', 'desc')
            ->first();

        $soldeDebut = 0;
        if ($lastClosed) {
            $balance = AccountDailyBalance::where('account_id', $accountId)
                ->where('agence_id', $agenceId)
                ->where('monnaie', $devise)
                ->where('cloture_comptable_id', $lastClosed->id)
                ->first();
            $soldeDebut = $balance ? $balance->solde_fin : 0;
        }

        // Mouvements du jour pour ce compte/devise
        $mouvements = \App\Models\JournalEntryLine::where('account_id', $accountId)
            ->where('monnaie', $devise)
            ->whereHas('journalEntry', function ($q) {
                $q->where('journee_comptable_id', $this->cloture->id)
                  ->where('agence_id', $this->cloture->agence_id);
            })
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $totalDebit = (float) ($mouvements->total_debit ?? 0);
        $totalCredit = (float) ($mouvements->total_credit ?? 0);

        return $soldeDebut + $totalDebit - $totalCredit;
    }

    public function render()
    {
        return view('livewire.clotures.clotures-show', [
            'cloture' => $this->cloture,
            'soldesCaisse' => $this->soldesCaisse,
            'stats' => $this->stats,
        ]);
    }
}