<?php

namespace App\Livewire\Depenses;

use App\Models\Depense;
use Livewire\Component;
use Livewire\WithPagination;

class DepenseList extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function delete($id)
    {
        $depense = Depense::findOrFail($id);
        $depense->delete();
        session()->flash('message', 'Dépense supprimée avec succès.');
    }

    public function render()
    {
        $depenses = Depense::with(['typeDepense', 'beneficiaire'])
            ->where('libelle', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.depenses.depense-list', [
            'depenses' => $depenses
        ])->layout('layouts.app');
    }
}