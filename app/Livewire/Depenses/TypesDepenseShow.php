<?php

namespace App\Livewire\Depenses;

use App\Models\TypesDepense;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TypesDepenseShow extends Component
{
    public TypesDepense $typesDepense;

    public function mount(TypesDepense $typesDepense)
    {
        $this->typesDepense = $typesDepense;
    }

    public function render()
    {
        return view('livewire.depenses.types-depense-show');
    }
}
