<?php

namespace App\Livewire\Revenus;

use App\Models\Revenu;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RevenuShow extends Component
{
    public Revenu $revenu;

    public function mount(Revenu $revenu)
    {
        $this->revenu = $revenu;
    }

    public function render()
    {
        return view('livewire.revenus.revenu-show');
    }
}