<?php

namespace App\Livewire\Account;

use App\Models\Agence;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class IncomeStatement extends Component
{
    public $agence_id = '';
    public $date_debut;
    public $date_fin;

    public function mount()
    {
        //Initialisation des dates
        $derniereCloture = \App\Models\CloturesComptable::latest('date_cloture')->first();
        $dateParDefaut = $derniereCloture 
            ? $derniereCloture->date_cloture->format('Y-m-d') 
            : now()->format('Y-m-d');

        $this->date_debut = $dateParDefaut;
        $this->date_fin = $dateParDefaut;
    }

    /**
     * Récupère les données groupées par compte en utilisant UNIQUEMENT le montant_base (CDF)
     */
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

        // On ventile le montant_base selon qu'il s'agissait d'un débit ou d'un crédit à l'origine
        return $query->selectRaw('
                account_id, 
                SUM(CASE WHEN debit > 0 THEN montant_base ELSE 0 END) as total_debit, 
                SUM(CASE WHEN credit > 0 THEN montant_base ELSE 0 END) as total_credit
            ')
            ->groupBy('account_id')
            ->get();
    }

    /**
     * Calcule le résultat global en CDF
     */
    public function getResultatProperty()
    {
        $stats = [
            'produits' => 0, 
            'charges' => 0, 
            'net' => 0
        ];

        foreach ($this->rows as $row) {
            if ($row->account->type === 'produit') {
                // Produit = Crédit - Débit
                $stats['produits'] += ($row->total_credit - $row->total_debit);
            } else {
                // Charge = Débit - Crédit
                $stats['charges'] += ($row->total_debit - $row->total_credit);
            }
        }

        $stats['net'] = $stats['produits'] - $stats['charges'];

        return $stats;
    }

    public function render()
    {
        return view('livewire.account.income-statement', [
            'agences' => Agence::all(),
            'comptesProduits' => $this->rows->filter(fn($r) => $r->account->type === 'produit'),
            'comptesCharges' => $this->rows->filter(fn($r) => $r->account->type === 'charge'),
        ]);
    }
}