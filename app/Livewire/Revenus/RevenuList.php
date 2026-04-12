<?php

namespace App\Livewire\Revenus;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\Revenu;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RevenuList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public $search = '';
    public $monnaie = '';
    public $date_debut;
    public $date_fin;
    
    // Filtres de périmètre (Spatie & Multi-agence)
    public $all_agents = false;
    public $selected_agence_id = null;

    public function mount()
    {
        $user = Auth::user();
        
        //Initialisation des dates
        $derniereCloture = \App\Models\CloturesComptable::latest('date_cloture')->first();
        $dateParDefaut = $derniereCloture 
            ? $derniereCloture->date_cloture->format('Y-m-d') 
            : now()->format('Y-m-d');

        $this->date_debut = $dateParDefaut;
        $this->date_fin = $dateParDefaut;

        if (!$user->can('can.level6')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = Revenu::with(['typeRevenu', 'agence', 'creator']);

        // --- SÉCURITÉ NIVEAU 3 : Admin Global ---
        if ($user->can('can.level6')) {
            $query->when($this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id));
        } 
        // --- SÉCURITÉ NIVEAU 1 & 2 : Restriction Agence ---
        else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- SÉCURITÉ NIVEAU 1 : Restriction Personnelle ---
        if (!$user->can('can.level4') || !$this->all_agents) {
            if (!$user->can('can.level6')) {
                $query->where('created_by', $user->id);
            }
        }

        // --- FILTRES DYNAMIQUES ---
        $query->when($this->search, function($q) {
            $q->where('libelle', 'like', '%' . $this->search . '%');
        })
        ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_operation', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_operation', '<=', $this->date_fin));

        return view('livewire.revenus.revenu-list', [
            'revenus' => $query->latest('date_operation')->paginate(20),
            'agences' => $user->can('can.level6') ? Agence::all() : []
        ]);
    }
}
