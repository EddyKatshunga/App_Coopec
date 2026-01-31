<?php

namespace App\Livewire\Membres;

use Livewire\Component;
use App\Models\Membre;

class ShowMembre extends Component
{
    public Membre $membre;
    public bool $showAddPhotoModal = false;

    public float $totalSoldeCDF = 0;
    public float $totalSoldeUSD = 0;

    public function mount(Membre $membre)
    {
        $this->membre = $membre->load([
            'user',
            'agent',
            'agentParrain',
            'comptes'
        ]);

        $this->calculerStatistiques();
    }

    private function calculerStatistiques(): void
    {
        $this->totalSoldeCDF = $this->membre->comptes->sum('solde_cdf');
        $this->totalSoldeUSD = $this->membre->comptes->sum('solde_usd');
    }

    public function telechargerFiche()
    {
        return redirect()->route('membre.fiche.pdf', $this->membre);
    }

    public function render()
    {
        return view('livewire.membres.show-membre')
            ->layout('layouts.app');
    }
}

