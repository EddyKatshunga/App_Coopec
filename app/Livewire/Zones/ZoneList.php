<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;

class ZoneList extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.zones.zone-list', [
            'zones' => Zone::with('gerant', 'agence')
                ->where('nom', 'like', '%' . $this->search . '%')
                ->paginate(10)
        ])->layout('layouts.app');
    }
}