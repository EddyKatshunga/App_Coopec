<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use Livewire\Component;

class ZoneShow extends Component
{
    public Zone $zone;

    public function mount($zoneId)
    {
        $this->zone = Zone::with(['gerant', 'agence', 'credits'])->findOrFail($zoneId);
    }

    public function render()
    {
        return view('livewire.zones.zone-show')
            ->layout('layouts.app');
    }
}