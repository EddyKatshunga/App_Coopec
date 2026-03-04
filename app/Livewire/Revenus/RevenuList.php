<?php

namespace App\Livewire\Revenus;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\Revenu;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RevenuList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public $search = '';

    public function delete($id)
    {
        $revenu = Revenu::findOrFail($id);
        $revenu->delete(); // Le trait AffectsCoffre devrait gérer la soustraction automatique
        session()->flash('message', 'Revenu supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.revenus.revenu-list', [
            'revenus' => Revenu::with('typeRevenu')
                ->where('libelle', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10)
        ]);
    }
}
