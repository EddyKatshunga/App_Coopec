<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class ZoneShow extends Component
{
    use AuthorizesRequests;

    public Zone $zone;

    public function mount(Zone $zone)
    {
        $this->authorize('view', $zone);
        $this->zone = $zone->load(['gerant', 'credits.remboursements', 'credits.membre']);
    }

    public function render()
    {
        $credits = $this->zone->credits;
        
        // On initialise les stats pour les deux monnaies
        $statsParMonnaie = [];

        foreach (['USD', 'CDF'] as $monnaie) {
            $creditsMonnaie = $credits->where('monnaie', $monnaie);
            
            $totalCapital = $creditsMonnaie->sum('capital');
            $totalInteret = $creditsMonnaie->sum('interet');
            $totalAttendu = $totalCapital + $totalInteret;
            $totalPaye = $creditsMonnaie->sum(fn($c) => $c->total_rembourse);

            $statsParMonnaie[$monnaie] = [
                'capital' => $totalCapital,
                'encours' => $creditsMonnaie->sum(fn($c) => $c->reste_du),
                'penalites' => $creditsMonnaie->sum(fn($c) => $c->penalites_courantes),
                'taux_recouvrement' => $totalAttendu > 0 ? ($totalPaye / $totalAttendu) * 100 : 0,
                'nb_credits' => $creditsMonnaie->count(),
                'nb_retards' => $creditsMonnaie->where('statut', 'en_retard')->count(),
            ];
        }

        return view('livewire.zones.zone-show', [
            'stats' => $statsParMonnaie,
            'credits_list' => $credits->sortByDesc('created_at')
        ]);
    }
}