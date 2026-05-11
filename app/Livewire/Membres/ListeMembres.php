<?php

namespace App\Livewire\Membres;

use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Membre;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ListeMembres extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Recherche & filtres
    public string $search = '';
    public string $sexe = '';
    public $agence_id = null;
    public string $qualite = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sexe' => ['except' => ''],
        'qualite' => ['except' => ''],
    ];

    public function mount()
    {
        // Par défaut, l'agence de l'utilisateur connecté
        $this->agence_id = auth()->user()->agence_id ?? Agence::first()->id ?? null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'sexe', 'qualite', 'dateFrom', 'dateTo']);
    }

    public function render()
    {
        // Sécurité : seul le level6 peut changer d'agence, sinon on force l'agence de l'user
        $currentAgenceId = auth()->user()->can('can.level4') 
            ? $this->agence_id 
            : auth()->user()->agence_id;

        $membres = Membre::query()
            ->with('user', 'agent', 'creditEnCours')
            ->where('agence_id', $currentAgenceId)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('numero_identification', 'like', '%' . $this->search . '%');
            })
            ->when($this->sexe, fn ($q) => $q->where('sexe', $this->sexe))
            ->when($this->qualite, fn ($q) => $q->where('qualite', $this->qualite))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('date_adhesion', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('date_adhesion', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(30);
        
        $agences = auth()->user()->can('can.level4') 
            ? Agence::orderBy('nom')->get() 
            : collect();

        return view('livewire.membres.liste-membres', [
            'membres' => $membres,
            'agences' => $agences
        ]);
    }
}