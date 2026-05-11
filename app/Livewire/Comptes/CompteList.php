<?php

namespace App\Livewire\Comptes;

use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Compte;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CompteList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $agence_id = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->agence_id = Auth::user()->agence_id ?? Agence::first()->id ?? null;
    }

    public function render()
    {
        $comptes = Compte::with(['user'])
                    ->where('agence_id', $this->agence_id)
                    ->when($this->search, function ($query) {
                        // On groupe les conditions OR dans une sous-requête
                        $query->where(function ($subQuery) {
                            $subQuery->where('numero_compte', 'like', '%' . $this->search . '%')
                                    ->orWhere('intitule', 'like', '%' . $this->search . '%')
                                    // On cherche dans la relation 'user'
                                    ->orWhereHas('user', function ($userQuery) {
                                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                                    });
                        });
                    })
                    ->latest()
                    ->paginate(100);

        $stats = [
            'total_comptes' => Compte::count(),
            'total_cdf'     => Compte::sum('solde_cdf'),
            'total_usd'     => Compte::sum('solde_usd'),
            'membres'       => Compte::distinct('membre_id')->count('membre_id'),
        ];

        $agences = Auth::user()->can('can.level1') 
            ? Agence::orderBy('nom')->get() 
            : collect();

        return view('livewire.comptes.compte-list', [
            'comptes' => $comptes,
            'stats'   => $stats,
            'agences' => $agences
        ]);
    }
}