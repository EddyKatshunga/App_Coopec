<?php

namespace App\Livewire\Depenses;

use App\Models\Depense;
use Livewire\Component;

class DepenseShow extends Component
{
    public Depense $depense;

    public function mount($id)
    {
        $this->depense = Depense::with(['typeDepense', 'beneficiaire', 'agence'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.depenses.depense-show')
            ->layout('layouts.app');
    }
}