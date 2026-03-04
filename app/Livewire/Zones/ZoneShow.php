<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ZoneShow extends Component
{
    public Zone $zone;

    public function mount($zone)
    {
        $this->zone = $zone;
    }

    public function render()
    {
        return view('livewire.zones.zone-show');
    }
}