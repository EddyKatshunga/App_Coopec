<?php

namespace App\Livewire\Depenses;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\Depense;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class DepenseList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public $search = '';
    public $monnaie = '';
    public $date_debut;
    public $date_fin;
    
    // Filtres de périmètre
    public $all_agents = false;
    public $selected_agence_id = null;

    public function mount()
    {
        $user = Auth::user();
        // Initialisation des dates (par défaut sur le mois en cours pour les dépenses)
        $this->date_debut = now()->startOfMonth()->format('Y-m-d');
        $this->date_fin = now()->format('Y-m-d');

        if (!$user->can('agence.view.all')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = Depense::with(['typeDepense', 'beneficiaire', 'agence', 'creator']);

        // --- SÉCURITÉ NIVEAU 3 : Admin ---
        if ($user->can('agence.view.all')) {
            $query->when($this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id));
        } 
        // --- SÉCURITÉ NIVEAU 1 & 2 : Restriction Agence ---
        else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- SÉCURITÉ NIVEAU 1 : Restriction Personnelle ---
        // On vérifie si l'utilisateur a le droit de voir toute l'agence
        if (!$user->can('agence.operations.view') || !$this->all_agents) {
            // Si pas admin global, on restreint au créateur
            if (!$user->can('agence.view.all')) {
                $query->where('created_by', $user->id);
            }
        }

        // --- FILTRES DYNAMIQUES ---
        $query->when($this->search, function($q) {
            $q->where(function($sub) {
                $sub->where('libelle', 'like', '%' . $this->search . '%')
                   ->orWhereHas('beneficiaire.user', fn($sq) => $sq->where('name', 'like', '%'.$this->search.'%'));
            });
        })
        ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_operation', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_operation', '<=', $this->date_fin));

        return view('livewire.depenses.depense-list', [
            'depenses' => $query->latest('date_operation')->paginate(20),
            'agences' => $user->can('agence.view.all') ? Agence::all() : []
        ]);
    }
}