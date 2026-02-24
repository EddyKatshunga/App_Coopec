<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use Livewire\Component;

class AgentShow extends Component
{
    public Agent $agent;

    public function mount(Agent $agent)
    {
        // On charge les relations nécessaires et les compteurs
        $this->agent = $agent->load([
            'user', 
            'agence', 
            'zone_dirige', 
            'agence_dirige',
            'membre'
        ])->loadCount([
            'membresAmenes',
            'credits',
            'transactions',
            'remboursements'
        ]);
    }

    public function render()
    {
        return view('livewire.agents.agent-show')
            ->layout('layouts.app');
    }
}
