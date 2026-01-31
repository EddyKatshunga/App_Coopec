<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Agence;
use App\Services\AgenceContext;

class AgenceSelector extends Component
{
    public $agenceId;

    public function mount()
    {
        $this->agenceId = AgenceContext::get()?->id;
    }

    public function updatedAgenceId()
    {
        AgenceContext::set($this->agenceId);
        $this->dispatch('agenceChanged');
    }

    public function render()
    {
        return view('livewire.dashboard.agence-selector', [
            'agences' => Agence::where('active', true)->get(),
        ]);
    }
}