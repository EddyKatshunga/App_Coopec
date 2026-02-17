<?php

namespace App\Livewire\Depenses;

use App\Models\TypesDepense;
use Livewire\Component;
use Livewire\WithPagination;

class TypesDepenseList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function delete($id)
    {
        TypesDepense::findOrFail($id)->delete();
        session()->flash('success', 'Type de dépense supprimé.');
    }

    public function render()
    {
        return view('livewire.depenses.types-depense-list', [
            'typesDepenses' => TypesDepense::latest()->paginate(10),
        ])->layout('layouts.app');;
    }
}
