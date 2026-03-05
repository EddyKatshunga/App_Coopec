<?php

namespace App\Livewire\Depenses;

use App\Models\Depense;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DepenseShow extends Component
{
    public Depense $depense;

    public function mount(Depense $depense)
    {
        $this->depense = $depense;
    }

    public function render()
    {
        return view('livewire.depenses.depense-show');
    }
}