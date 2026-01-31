<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\AgenceContext;

class Stats extends Component
{
    public function render()
    {
        $agence = AgenceContext::get();

        return view('livewire.dashboard.stats', [
            'creditCount' => $agence
                ? $agence->credits()->count()
                : 0,
        ]);
    }
}

