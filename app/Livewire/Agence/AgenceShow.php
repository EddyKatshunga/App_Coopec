<?php

namespace App\Livewire\Agence;

use Livewire\Component;
use App\Models\Agence;
use App\Models\Agent;
use App\Services\AgenceService;

class AgenceShow extends Component
{
    public Agence $agence;

    public $showModal = false;
    public $nouveauDirecteurId = null;
    public $confirmation = false;

    protected $rules = [
        'nouveauDirecteurId' => 'required|exists:agents,id',
        'confirmation' => 'accepted'
    ];

    public function mount(Agence $agence)
    {
        $this->agence = $agence->load(['directeur.user', 'agents.user']);
    }

    public function ouvrirModal()
    {
        if ($this->agence->agents->count() === 0) {
            session()->flash('error', 'Aucun agent disponible pour devenir directeur.');
            return;
        }

        $this->reset(['nouveauDirecteurId', 'confirmation']);
        $this->showModal = true;
    }

    public function changerDirecteur(AgenceService $service)
    {
        $this->validate();

        $agent = Agent::findOrFail($this->nouveauDirecteurId);

        $service->changerDirecteur($this->agence, $agent);

        $this->agence->refresh()->load(['directeur.user', 'agents.user']);

        $this->showModal = false;

        session()->flash('success', 'Directeur modifié avec succès.');
    }

    public function render()
    {
        return view('livewire.agence.agence-show');
    }
}
