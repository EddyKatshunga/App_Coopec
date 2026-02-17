<?php

namespace App\Livewire\Depenses;

use App\Models\TypesDepense;
use Livewire\Component;

class TypesDepenseShow extends Component
{
    public TypesDepense $typesDepense;

    public function mount(TypesDepense $typesDepense)
    {
        $this->typesDepense = $typesDepense;
    }

    public function render()
    {
        return view('livewire.depenses.types-depense-show')
            ->layout('layouts.app');;
    }
}
