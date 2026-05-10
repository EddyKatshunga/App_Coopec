<?php

namespace App\Livewire\Credits;

use App\Models\Credit;
use App\Models\Zone;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
class CreditsList extends Component
{
    use WithPagination;

    // Filtres
    public string $search = '';
    public string $statut = 'en_cours';
    public $selected_agence_id = null;
    public ?string $selected_zone_id = null;
    public string $monnaie = '';

    // Pagination
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->selected_agence_id = Auth::user()->agence_id ?? Agence::first()->id ?? null;
    }

    /**
     * Réinitialiser la pagination à chaque changement de filtre
     */
    public function updated($property)
    {
        if (in_array($property, ['search', 'statut', 'selected_agence_id', 'selected_zone_id', 'monnaie'])) {
            $this->resetPage();
        }
    }

    /**
     * Construire la requête des crédits avec les filtres
     */
    protected function getCreditsQuery(): Builder
    {
        $query = Credit::query()
            ->with(['membre', 'agent', 'zone'])
            ->select([
                'credits.id',
                'credits.uuid',
                'credits.numero_credit',
                'credits.monnaie',
                'credits.capital',
                'credits.interet',
                'credits.total_remboursement',
                'credits.duree',
                'credits.unite_temps',
                'credits.date_fin_prevue',
                'credits.statut',
                'credits.membre_id',
                'credits.agent_id',
                'credits.zone_id',
                'credits.agence_id'
            ]);

        // Filtre par statut (en_cours, termine)
        if (!empty($this->statut)) {
            $query->where('statut', $this->statut);
        }

        // Filtre par agence (pour super admin ou admin)
        if (!empty($this->selected_agence_id)) {
            $query->where('agence_id', $this->selected_agence_id);
        }

        // Filtre par zone
        if (!empty($this->selected_zone_id)) {
            $query->where('zone_id', $this->selected_zone_id);
        }

        // Filtre par devise
        if (!empty($this->monnaie)) {
            $query->where('monnaie', $this->monnaie);
        }

        // Recherche par numéro de crédit ou nom du membre
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('numero_credit', 'LIKE', '%' . $this->search . '%')
                  ->orWhereHas('membre', function ($sub) {
                      $sub->where('nom', 'LIKE', '%' . $this->search . '%');
                  });
            });
        }

        // Tri par date de création décroissante (les plus récents d'abord)
        $query->latest('created_at');

        return $query;
    }

    /**
     * Récupérer les zones disponibles en fonction de l'agence sélectionnée
     */
    public function getZonesProperty()
    {
        if (empty($this->selected_agence_id)) {
            return collect();
        }
        return Zone::where('agence_id', $this->selected_agence_id)->get();
    }

    public function render()
    {
        $credits = $this->getCreditsQuery()->paginate(15);

        $agences = Auth::user()->can('can.level6') ? Agence::all() : [];

        return view('livewire.credits.credits-list', [
            'credits' => $credits,
            'agences' => $agences,
            'zones' => $this->zones,
        ]);
    }
}