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
        // On charge les crédits avec les remboursements pour les calculs de performance
        $this->zone = $zone->load(['gerant', 'credits.remboursements', 'credits.membre']);
    }

    public function render()
    {
        $credits = $this->zone->credits;
        
        // Calculs de performance pour le Dashboard
        $stats = [
            'total_capital' => $credits->sum('capital'),
            'total_interet' => $credits->sum('interet'),
            'total_encours' => $credits->sum(fn($c) => $c->reste_du),
            'total_penalites' => $credits->sum(fn($c) => $c->penalites_courantes),
            'nb_credits_actifs' => $credits->whereIn('statut', ['en_cours', 'en_retard'])->count(),
            'nb_retards' => $credits->where('statut', 'en_retard')->count(),
        ];

        // Calcul du taux de recouvrement (Capital + Intérêt payé / Total attendu)
        $totalAttendu = $stats['total_capital'] + $stats['total_interet'];
        $totalPaye = $credits->sum(fn($c) => $c->total_rembourse);
        $stats['taux_recouvrement'] = $totalAttendu > 0 ? ($totalPaye / $totalAttendu) * 100 : 0;

        return view('livewire.zones.zone-show', [
            'stats' => $stats,
            'credits_list' => $credits->sortByDesc('created_at')
        ]);
    }
}