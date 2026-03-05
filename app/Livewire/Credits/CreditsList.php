<?php

namespace App\Livewire\Credits;

use App\Models\Credit;
use App\Models\Zone;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CreditsList extends Component
{
    use WithPagination;

    public $search = '';
    public $zone_id = null;
    public $statut = null;
    public $date_debut = null;
    public $date_fin = null;
    
    // Filtres de périmètre
    public $selected_agence_id = null;
    public $all_agents = false;

    public function mount()
    {
        $user = Auth::user();
        if (!$user->can('agence.view.all')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        // Optimisation Eager Loading : on charge tout pour éviter les requêtes N+1 dans la grille
        $query = Credit::with(['user', 'zone', 'remboursements', 'agence', 'creator']);

        // --- SÉCURITÉ NIVEAU 3 : Admin Global ---
        if ($user->can('agence.view.all')) {
            $query->when($this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id));
        } 
        // --- SÉCURITÉ NIVEAU 1 & 2 : Restriction Agence ---
        else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- SÉCURITÉ NIVEAU 1 : Restriction Personnelle ---
        if (!$user->can('agence.operations.view') || !$this->all_agents) {
            if (!$user->can('agence.view.all')) {
                $query->where('created_by', $user->id);
            }
        }

        // --- FILTRES MÉTIERS ---
        $query->when($this->search, function($q) {
            $q->where(function($sub) {
                $sub->where('numero_credit', 'like', "%{$this->search}%")
                   ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        })
        ->when($this->zone_id, fn($q) => $q->where('zone_id', $this->zone_id))
        ->when($this->statut, fn($q) => $q->where('statut', $this->statut))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_credit', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_credit', '<=', $this->date_fin));

        return view('livewire.credits.credits-list', [
            'credits' => $query->latest('date_credit')->paginate(12),
            'zones' => Zone::orderBy('nom')->get(),
            'agences' => $user->can('agence.view.all') ? Agence::all() : []
        ]);
    }
}