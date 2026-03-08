<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class StatsMembreMini extends Component
{
    public function render()
    {
        $membre = auth()->user()->membre;
        return view('livewire.dashboard.stats-membre-mini', [
            'solde_total_cdf' => $membre ? $membre->comptes->sum('solde_cdf') : 0,
            'solde_total_usd' => $membre ? $membre->comptes->sum('solde_usd') : 0,
            'prochaine_echeance' => $membre ? $membre->credits()->where('statut', 'en_cours')->first() : null
        ]);
    }
}
