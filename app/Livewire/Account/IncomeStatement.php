<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Models\AccountDailyBalance;
use App\Models\Agence;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class IncomeStatement extends Component
{
    public $agence_id = null;
    public $date_debut;
    public $date_fin;
    public $resultatAntérieur = 0;

    public function mount()
    {
        $dateParDefaut = now()->format('Y-m-d');
        $this->date_debut = $dateParDefaut;
        $this->date_fin = $dateParDefaut;
        $this->agence_id = Auth::user()->agence_id ?? Agence::first()->id;
        $this->calculerResultatAntérieur();
    }

    /**
     * Surveille les changements des filtres pour recalculer le résultat antérieur.
     */
    public function updated($property)
    {
        if (in_array($property, ['agence_id', 'date_debut', 'date_fin'])) {
            $this->calculerResultatAntérieur();
        }
    }

    protected function calculerResultatAntérieur()
    {
        $compteResultat = Account::where('numero', '12')->first();
        if (!$compteResultat) return;

        $dateAvant = \Carbon\Carbon::parse($this->date_debut)->subDay()->toDateString();

        $lastBalance = AccountDailyBalance::getAccountDailyBalanceForDate(
            $this->agence_id,
            $compteResultat,
            'CDF',
            $dateAvant
        );
        $this->resultatAntérieur = (float) ($lastBalance?->solde_fin ?? 0);
    }

    public function getRowsProperty()
    {
        $query = JournalEntryLine::query()
            ->with('account')
            ->whereHas('account', function ($q) {
                $q->whereIn('type', ['charge', 'produit']);
            })
            ->whereHas('journalEntry', function ($q) {
                if ($this->agence_id) $q->where('agence_id', $this->agence_id);
                $q->whereBetween('date_operation', [
                    \Carbon\Carbon::parse($this->date_debut)->startOfDay(),
                    \Carbon\Carbon::parse($this->date_fin)->endOfDay()
                ]);
            });

        return $query->selectRaw('
                account_id, 
                SUM(CASE WHEN debit > 0 THEN montant_base ELSE 0 END) as total_debit, 
                SUM(CASE WHEN credit > 0 THEN montant_base ELSE 0 END) as total_credit
            ')
            ->groupBy('account_id')
            ->get();
    }

    public function getResultatProperty()
    {
        $stats = [
            'produits' => 0,
            'charges' => 0,
            'net_periode' => 0,
            'resultat_antérieur' => $this->resultatAntérieur,
            'cumul' => 0,
        ];

        foreach ($this->rows as $row) {
            if ($row->account->type === 'produit') {
                $stats['produits'] += $row->total_credit;
            } else {
                $stats['charges'] += $row->total_debit;
            }
        }

        $stats['net_periode'] = $stats['produits'] - $stats['charges'];
        $stats['cumul'] = $stats['resultat_antérieur'] + $stats['net_periode'];

        return $stats;
    }

    public function render()
    {
        return view('livewire.account.income-statement', [
            'agences' => Agence::all(),
            'comptesProduits' => $this->rows->filter(fn($r) => $r->account->type === 'produit'),
            'comptesCharges' => $this->rows->filter(fn($r) => $r->account->type === 'charge'),
            'resultatAntérieur' => $this->resultatAntérieur,
        ]);
    }
}