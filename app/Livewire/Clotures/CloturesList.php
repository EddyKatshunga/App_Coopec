<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use App\Models\Agence;
use App\Services\ClotureService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesList extends Component
{
    use WithPagination;

    // Filtres
    public $agenceId;
    public $dateDebut;
    public $dateFin;

    public function mount()
    {
        // Par défaut, l'agence de l'utilisateur
        $this->agenceId = auth()->user()->agence_id;
    }

    // Réinitialise la pagination lors d'un changement de filtre
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['agenceId', 'dateDebut', 'dateFin'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = CloturesComptable::query();

        // Filtre par Agence (obligatoire par défaut)
        if ($this->agenceId) {
            $query->where('agence_id', $this->agenceId);
        }

        // Filtre par Date de départ
        if ($this->dateDebut) {
            $query->whereDate('date_cloture', '>=', $this->dateDebut);
        }

        // Filtre par Date de fin
        if ($this->dateFin) {
            $query->whereDate('date_cloture', '<=', $this->dateFin);
        }

        $clotures = $query->orderBy('date_cloture', 'desc')->paginate(10);

        // On vérifie si une journée est ouverte pour l'agence sélectionnée
        $journeeOuverte = CloturesComptable::where('agence_id', $this->agenceId)
            ->where('statut', 'ouverte')
            ->exists();

        // Liste des agences pour le filtre (uniquement si l'utilisateur a le droit)
        // Remplacez 'view-all-agencies' par votre gate/permission réelle
        $agences = auth()->user()->can('view-all-agencies') ? Agence::all() : [];

        return view('livewire.clotures.clotures-list', [
            'clotures' => $clotures,
            'journeeOuverte' => $journeeOuverte,
            'agences' => $agences,
        ]);
    }
}