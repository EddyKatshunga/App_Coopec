<?php

namespace App\Livewire\Membres;

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
    public string $qualite = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sexe' => ['except' => ''],
        'qualite' => ['except' => ''],
    ];

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
        $membres = Membre::query()
            ->with('user', 'agent')
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

        return view('livewire.membres.liste-membres', compact('membres'));
    }
}