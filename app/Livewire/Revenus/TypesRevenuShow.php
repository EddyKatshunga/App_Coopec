<?php

namespace App\Livewire\Revenus;

use App\Models\TypesRevenu;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[LayoUt('layouts.app')]
class TypesRevenuShow extends Component
{
    public TypesRevenu $typesRevenu;

    public function mount(TypesRevenu $typesRevenu)
    {
        $this->typesRevenu = $typesRevenu;
    }
    
    public function render()
    {
        return view('livewire.revenus.types-revenu-show');
    }
}
