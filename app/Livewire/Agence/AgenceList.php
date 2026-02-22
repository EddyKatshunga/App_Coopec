<?php

namespace App\Livewire\Agence;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Agence;

class AgenceList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $agences = Agence::with('chefAgence.user')
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('ville', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.agence.agence-list', compact('agences'))
            ->layout('layouts.app');
    }
}
