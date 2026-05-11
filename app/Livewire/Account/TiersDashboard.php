<?php

namespace App\Livewire\Account;

use Livewire\Component;
use App\Models\Account;
use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TiersDashboard extends Component
{
    public $monnaie = 'CDF';
    public $date_debut;
    public $date_fin;
    public $agence_id;

    public $isSuperAdmin = false;

    public function mount()
    {
        $user = Auth::user();
        $this->isSuperAdmin = $user->hasPermissionTo('can.level6');

        $this->date_debut = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_fin = Carbon::now()->format('Y-m-d');

        if ($this->isSuperAdmin) {
            $this->agence_id = $user->agence_id ?? Agence::first()?->id;
        } else {
            $this->agence_id = $user->agence_id;
        }
    }

    /**
     * Récupération des données avec reports via clôture comptable
     */
    public function getDonneesProperty()
    {
        $compteDette = Account::where('numero', '40')->first();
        $compteCreance = Account::where('numero', '45')->first();

        if (!$compteDette || !$compteCreance) {
            return $this->emptyData();
        }

        $agenceId = $this->agence_id;

        // Requête de base pour les mouvements de la période (avec filtre agence et dates)
        $baseQuery = JournalEntryLine::whereHas('journalEntry', function ($q) use ($agenceId) {
            $q->whereBetween('date_operation', [
                    Carbon::parse($this->date_debut)->startOfDay(),
                    Carbon::parse($this->date_fin)->endOfDay()
                ])
              ->when($agenceId, fn($q2) => $q2->where('agence_id', $agenceId));
        })->where('monnaie', $this->monnaie);

        // Totaux période pour Dette (40)
        $totauxDette = (clone $baseQuery)->where('account_id', $compteDette->id)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        // Totaux période pour Créance (45)
        $totauxCreance = (clone $baseQuery)->where('account_id', $compteCreance->id)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $periodDebitDette = (float) ($totauxDette->total_debit ?? 0);
        $periodCreditDette = (float) ($totauxDette->total_credit ?? 0);
        $periodDebitCreance = (float) ($totauxCreance->total_debit ?? 0);
        $periodCreditCreance = (float) ($totauxCreance->total_credit ?? 0);

        // Solde initial (avant la période) via la clôture précédente
        $soldeInitialDette = null;
        $soldeInitialCreance = null;

        if ($agenceId) {
            $dateAvant = Carbon::parse($this->date_debut)->subDay()->toDateString();
            $balanceDette = \App\Models\AccountDailyBalance::getAccountDailyBalanceForDate(
                $agenceId,
                $compteDette,
                $this->monnaie,
                $dateAvant
            );
            $balanceCreance = \App\Models\AccountDailyBalance::getAccountDailyBalanceForDate(
                $agenceId,
                $compteCreance,
                $this->monnaie,
                $dateAvant
            );
            $soldeInitialDette = (float) ($balanceDette?->solde_fin ?? 0);
            $soldeInitialCreance = (float) ($balanceCreance?->solde_fin ?? 0);

        }

        // Calcul des soldes finaux selon nature des comptes
        // Dette (passif) : solde final = solde_initial + (crédit - débit)
        // Créance (actif) : solde final = solde_initial + (débit - crédit)
        $soldeFinalDette = $soldeInitialDette !== null
            ? $soldeInitialDette - $periodCreditDette + $periodDebitDette
            : null;

        $soldeFinalCreance = $soldeInitialCreance !== null
            ? $soldeInitialCreance + $periodDebitCreance - $periodCreditCreance
            : null;

        // Récupération des lignes détaillées pour les tableaux
        $lignesDettes = (clone $baseQuery)->where('account_id', $compteDette->id)
            ->with('journalEntry')
            ->orderBy('id', 'desc')
            ->get();

        $lignesCreances = (clone $baseQuery)->where('account_id', $compteCreance->id)
            ->with('journalEntry')
            ->orderBy('id', 'desc')
            ->get();

        return [
            'dettes' => [
                'lignes' => $lignesDettes,
                'nouvelles' => $periodCreditDette,
                'payees' => $periodDebitDette,
                'solde_initial' => $soldeInitialDette,
                'solde_final' => $soldeFinalDette,
                'has_balance' => $soldeInitialDette !== null,
            ],
            'creances' => [
                'lignes' => $lignesCreances,
                'nouvelles' => $periodDebitCreance,
                'encaissees' => $periodCreditCreance,
                'solde_initial' => $soldeInitialCreance,
                'solde_final' => $soldeFinalCreance,
                'has_balance' => $soldeInitialCreance !== null,
            ],
            'position_nette' => ($soldeFinalDette ?? 0) + ($soldeFinalCreance ?? 0),
        ];
    }

    private function emptyData()
    {
        return [
            'dettes' => [
                'lignes' => collect(),
                'nouvelles' => 0,
                'payees' => 0,
                'solde_initial' => null,
                'solde_final' => 0,
                'has_balance' => false,
            ],
            'creances' => [
                'lignes' => collect(),
                'nouvelles' => 0,
                'encaissees' => 0,
                'solde_initial' => null,
                'solde_final' => 0,
                'has_balance' => false,
            ],
            'position_nette' => 0
        ];
    }

    public function render()
    {
        $agences = $this->isSuperAdmin ? Agence::orderBy('nom')->get() : collect();

        return view('livewire.account.tiers-dashboard', [
            'data' => $this->donnees,
            'agences' => $agences,
        ]);
    }
}