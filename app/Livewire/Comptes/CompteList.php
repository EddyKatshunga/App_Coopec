<?php

namespace App\Livewire\Comptes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Compte;

class CompteList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $comptes = Compte::with(['user'])
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

        return view('livewire.comptes.compte-list', [
            'comptes' => $comptes,
            'stats'   => $stats,
        ])->layout('layouts.app');
    }
}