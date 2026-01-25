<?php

namespace App\Livewire\Comptes;

use Livewire\Component;
use App\Models\Compte;

class ShowCompte extends Component
{
    public Compte $compte;

    public float $totalDepotCDF = 0;
    public float $totalRetraitCDF = 0;
    public float $totalDepotUSD = 0;
    public float $totalRetraitUSD = 0;

    public function mount(Compte $compte)
    {
        $this->compte = $compte->load([
            'membre',
            'transactions' => function ($query) {
                $query->orderBy('date_transaction');
            }
        ]);

        $this->calculerStatistiques();
    }

    private function calculerStatistiques(): void
    {
        $this->totalDepotCDF = $this->compte->transactions
            ->where('monnaie', 'CDF')
            ->where('type_transaction', 'DEPOT')
            ->sum('montant');

        $this->totalRetraitCDF = $this->compte->transactions
            ->where('monnaie', 'CDF')
            ->where('type_transaction', 'RETRAIT')
            ->sum('montant');

        $this->totalDepotUSD = $this->compte->transactions
            ->where('monnaie', 'USD')
            ->where('type_transaction', 'DEPOT')
            ->sum('montant');

        $this->totalRetraitUSD = $this->compte->transactions
            ->where('monnaie', 'USD')
            ->where('type_transaction', 'RETRAIT')
            ->sum('montant');
    }

    public function render()
    {
        return view('livewire.comptes.show-compte');
    }
}
