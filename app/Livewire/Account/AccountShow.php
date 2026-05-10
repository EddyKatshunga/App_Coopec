<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Models\AccountDailyBalance;
use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Models\JournalEntryLine;
use App\Services\AccountDailyBalanceService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class AccountShow extends Component
{
    use WithPagination;

    public Account $account;
    
    // Filtres
    public $agence_id = null;
    public $date_debut;
    public $date_fin;

    protected AccountDailyBalanceService $balanceService;

    public function boot(AccountDailyBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function mount(Account $account)
    {
        $this->account = $account->load(['parent', 'children']);
        $this->date_debut = now()->format('Y-m-d');
        $this->date_fin = now()->format('Y-m-d');
        $this->agence_id = Auth::user()->agence_id ?? Agence::first()->id;
    }

    public function updating($property)
    {
        if (in_array($property, ['agence_id', 'date_debut', 'date_fin'])) {
            $this->resetPage();
        }
    }

    /**
     * Calcul des statistiques (Soldes USD et CDF) avec report via AccountDailyBalance
     */
    public function getStatsProperty()
    {
        $devises = ['USD', 'CDF'];
        $stats = [];

        // Requête de base pour les mouvements de la période
        $baseQuery = JournalEntryLine::where('account_id', $this->account->id)
            ->whereHas('journalEntry', function ($q) {
                if ($this->agence_id) {
                    $q->where('agence_id', $this->agence_id);
                }
                $q->whereBetween('date_operation', [
                    Carbon::parse($this->date_debut)->startOfDay(),
                    Carbon::parse($this->date_fin)->endOfDay()
                ]);
            });

        // Totaux période par devise
        $totalsPeriod = $baseQuery->selectRaw('
                monnaie, 
                SUM(debit) as total_debit, 
                SUM(credit) as total_credit
            ')
            ->groupBy('monnaie')
            ->get()
            ->keyBy('monnaie');

        foreach ($devises as $devise) {
            $periodDebit = (float) ($totalsPeriod->get($devise)?->total_debit ?? 0);
            $periodCredit = (float) ($totalsPeriod->get($devise)?->total_credit ?? 0);

            // Déterminer le solde initial selon qu'une agence est filtrée ou non
            $soldeInitial = null;
            $dateAvant = Carbon::parse($this->date_debut)->subDay()->toDateString();
            $balance = AccountDailyBalance::getAccountDailyBalanceForDate(
                $this->agence_id,
                $this->account,
                $devise,
                $dateAvant
            );
            $soldeInitial = (float) ($balance?->solde_fin ?? 0);

            // Application de la règle de calcul du solde selon le type de compte
            if($this->account->type === 'charge' || $this->account->type === 'produit'){
                $variationPeriode = $periodCredit - $periodDebit;
            }else{
                $variationPeriode = $periodDebit - $periodCredit;
            }
            
            $soldeFinal = $soldeInitial !== null 
                ? $soldeInitial + $variationPeriode 
                : null;

            $stats[$devise] = [
                'debit'         => $periodDebit,
                'credit'        => $periodCredit,
                'solde_initial' => $soldeInitial,
                'solde_final'   => $soldeFinal,
                'has_balance'   => ($soldeInitial !== null), // indicateur pour l'affichage
            ];
        }

        return $stats;
    }

    public function render()
    {
        // Sécurité : seul le level6 peut changer d'agence, sinon on force l'agence de l'user
        $currentAgenceId = auth()->user()->can('can.level6') 
            ? $this->agence_id 
            : auth()->user()->agence_id;

        $lines = JournalEntryLine::query()
            ->with(['journalEntry.agence'])
            ->where('account_id', $this->account->id)
            ->whereHas('journalEntry', function ($q) {
                if ($this->agence_id) {
                    $q->where('agence_id', $this->agence_id);
                }
                $q->whereBetween('date_operation', [
                    Carbon::parse($this->date_debut)->startOfDay(),
                    Carbon::parse($this->date_fin)->endOfDay()
                ]);
            })
            ->orderBy('id', 'asc') // ou 'date_operation' selon besoin
            ->paginate(100);

        $agences = auth()->user()->can('can.level6') 
            ? Agence::orderBy('nom')->get() 
            : collect();

        return view('livewire.account.account-show', [
            'mouvements' => $lines,
            'agences'    => $agences,
            'stats'      => $this->stats,
        ]);
    }
}