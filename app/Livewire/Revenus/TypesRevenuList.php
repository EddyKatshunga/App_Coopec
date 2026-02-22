<?php

namespace App\Livewire\Revenus;

use App\Models\TypesRevenu;
use Livewire\Component;
use Livewire\WithPagination;

class TypesRevenuList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function delete($id)
    {
        TypesRevenu::findOrFail($id)->delete();
        session()->flash('success', 'Type de dépense supprimé.');
    }

    public function render()
    {
        return view('livewire.revenus.types-revenu-list', [
            'typesRevenus' => TypesRevenu::latest()->paginate(10),
        ])->layout('layouts.app');
    }
    
}
