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
        
        // On ne charge que le gérant pour l'affichage de l'en-tête.
        // Les KPIs sont calculés via les accesseurs du modèle.
        $this->zone = $zone->load('gerant');
    }

    public function render()
    {
        // Récupération du tableau de bord décisionnel depuis le modèle
        $dashboard = $this->zone->getDashboardData();

        // Récupération des crédits actifs pour la liste détaillée
        $creditsActifs = $this->zone->creditsActifs()
            ->with(['membre', 'remboursements']) // On garde le eager loading pour la liste
            ->latest()
            ->get();

        return view('livewire.zones.zone-show', [
            'dashboard'    => $dashboard,
            'credits_list' => $creditsActifs,
        ]);
    }
}