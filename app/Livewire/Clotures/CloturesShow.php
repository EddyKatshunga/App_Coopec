<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use App\Models\CloturesComptable;

class CloturesShow extends Component
{
    public CloturesComptable $cloture;

    public function render()
    {
        return view('livewire.clotures.clotures-show')
            ->layout('layouts.app');
    }
}