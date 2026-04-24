<?php

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
    
    // Le dashboard peut rester public s'il est petit (quelques scalaires)
    // Mais on l'initialise vide, il sera calculé au mount
    public array $dashboard = [];

    public function mount(Zone $zone): void
    {
        $this->authorize('view', $zone);
        
        // La zone charge uniquement ses infos de base
        $this->zone = $zone->load('gerant');
        
        // Le dashboard est généré 100% via le Modèle (SQL) sans charger les relations lourdes
        $this->dashboard = $this->zone->getDashboardData();
    }

    #[Computed]
    public function creditsList()
    {
        // On pagine la requête au lieu de faire un ->get() massif
        // L'attribut Computed empêche la sérialisation dans Livewire
        return $this->zone->credits()
            ->actif()
            ->with(['membre']) // Ne chargez 'remboursements' ici que si vous les affichez dans le tableau Blade !
            ->orderByDesc('date_credit')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.zones.zone-show', [
            // On appelle la propriété computed via $this->creditsList
            'credits_list' => $this->creditsList, 
        ]);
    }
}