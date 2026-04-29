<?php

namespace App\Models; // Rappel : Assurez-vous d'avoir les scopes dans Credit

namespace App\Livewire\Zones;

use App\Models\Zone;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class ZoneShow extends Component
{
    use AuthorizesRequests, WithPagination;

    public Zone $zone;
    public $search = '';

    public function mount(Zone $zone): void
    {
        $this->authorize('view', $zone);
        $this->zone = $zone->load(['gerant', 'agence']);
    }

    #[Computed]
    public function statsUsd()
    {
        return $this->zone->getStatsPortefeuille('USD');
    }

    #[Computed]
    public function statsCdf()
    {
        return $this->zone->getStatsPortefeuille('CDF');
    }

    #[Computed]
    public function credits()
    {
        return $this->zone->credits()
            ->enCours()
            ->with(['membre', 'agent'])
            ->where(function($q) {
                $q->where('numero_credit', 'like', "%{$this->search}%")
                  ->orWhereHas('membre', fn($query) => $query->where('nom', 'like', "%{$this->search}%"));
            })
            ->orderByRaw('date_fin_prevue < ? DESC', [now()]) // Met les retards en haut
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.zones.zone-show');
    }
}