<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ZoneList extends Component
{
    use WithPagination;

    public $selectedAgenceId = null;

    public function mount()
    {
        // Par défaut, on prend l'agence de l'utilisateur
        $this->selectedAgenceId = auth()->user()->agence_id ?? null;
    }

    public function render()
    {
        // Si l'utilisateur a le droit, il peut voir les zones de l'agence sélectionnée
        // sinon, on force son agence_id par sécurité
        $agenceId = auth()->user()->can('can.level6') 
            ? $this->selectedAgenceId 
            : auth()->user()->agence_id;

        $zones = Zone::where('agence_id', $agenceId)
            ->with(['gerant', 'agence'])
            ->latest()
            ->paginate(12);

        $agences = auth()->user()->can('can.level6') 
            ? Agence::orderBy('nom')->get() 
            : collect();

        return view('livewire.zones.zone-list', [
            'zones' => $zones,
            'agences' => $agences
        ]);
    }
}