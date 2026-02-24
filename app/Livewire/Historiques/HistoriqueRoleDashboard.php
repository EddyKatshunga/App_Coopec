<?php

namespace App\Livewire\Historiques;

use App\Models\HistoriqueRole;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class HistoriqueRoleDashboard extends Component
{
    use WithPagination;

    // Filtres
    public $searchUser = '';
    public $roleFilter = '';
    public $dateDebut;
    public $dateFin;

    // Réinitialiser la pagination lors d'une recherche
    public function updatingSearchUser() { $this->resetPage(); }
    public function updatingRoleFilter() { $this->resetPage(); }

    public function render()
    {
        $query = HistoriqueRole::with(['user', 'ancienRole', 'nouveauRole', 'creator'])
            ->latest();

        // Filtre par nom d'utilisateur
        if ($this->searchUser) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->searchUser . '%');
            });
        }

        // Filtre par rôle (ancien ou nouveau)
        if ($this->roleFilter) {
            $query->where(function ($q) {
                $q->where('ancien_role', $this->roleFilter)
                  ->orWhere('nouveau_role', $this->roleFilter);
            });
        }

        // Filtre par plage de dates
        if ($this->dateDebut) {
            $query->whereDate('created_at', '>=', $this->dateDebut);
        }
        if ($this->dateFin) {
            $query->whereDate('created_at', '<=', $this->dateFin);
        }

        return view('livewire.historiques.historique-role-dashboard', [
            'historiques' => $query->paginate(15),
            'roles' => Role::all()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['searchUser', 'roleFilter', 'dateDebut', 'dateFin']);
    }
}