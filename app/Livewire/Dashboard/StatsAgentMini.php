<?php

namespace App\Livewire\Dashboard;

use App\Models\Membre;
use App\Models\Transaction;
use Livewire\Component;

class StatsAgentMini extends Component
{
    public function render()
    {
        $today = auth()->user()->journee_ouverte?->date_cloture;
        
        return view('livewire.dashboard.stats-agent-mini', [
            'collecte_jour_cdf' => Transaction::where('created_by', auth()->user()->id)
                ->where('type_transaction', 'DEPOT')
                ->where('monnaie', 'CDF')
                ->whereDate('date_transaction', $today)
                ->sum('montant'),
            'collecte_jour_usd' => Transaction::where('created_by', auth()->user()->id)
                ->where('type_transaction', 'DEPOT')
                ->where('monnaie', 'USD')
                ->whereDate('date_transaction', $today)
                ->sum('montant'),
            'nouveaux_membres' => Membre::where('agent_parrain_id', auth()->user()->id)
                ->whereDate('created_at', $today)
                ->count()
        ]);
    }
}
