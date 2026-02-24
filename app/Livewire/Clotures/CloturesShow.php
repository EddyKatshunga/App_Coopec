<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CloturesShow extends Component
{
    public CloturesComptable $cloture;

    public function render()
    {
        return view('livewire.clotures.clotures-show');
    }
}