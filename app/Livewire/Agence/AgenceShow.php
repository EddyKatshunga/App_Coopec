<?php

namespace App\Livewire\Agence;

use Livewire\Component;
use App\Models\Agence;
use App\Models\Agent;
use App\Services\AgenceService;
use Illuminate\Support\Facades\Hash;

class AgenceShow extends Component
{
    public Agence $agence;

    public $showModal = false;
    public $nouveauDirecteurId = null;
    public $confirmation = false;
    public $motDePasse = '';
    public $motDePasseError = null;

    protected $rules = [
        'nouveauDirecteurId' => 'required|exists:agents,id',
        'confirmation' => 'accepted',
        'motDePasse' => 'required',
    ];

    public function mount(Agence $agence)
    {
        $this->agence = $agence->load(['chefAgence']);
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

        $this->reset('motDePasseError');

        if (!Hash::check($this->motDePasse, auth()->user()->password)) {
            $this->motDePasseError = 'Mot de passe incorrect.';
            return;
        }

        $service->changerDirecteur($this->agence, $this->nouveauDirecteurId);

        $this->agence->refresh()->load(['chefAgence.user', 'agents.user']);

        $this->showModal = false;

        session()->flash('success', 'Directeur modifié avec succès.');
    }

    public function render()
    {
        $depenses = $this->agence->depenses;
        return view('livewire.agence.agence-show', compact('depenses'))
            ->layout('layouts.app');
    }
}
