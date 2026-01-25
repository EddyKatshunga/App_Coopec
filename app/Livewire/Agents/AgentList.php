<?php

namespace App\Livewire\Agents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Agent;
use App\Services\AgentService;

class AgentList extends Component
{
    use WithPagination;

    public $search = '';
    public $statut = '';
    public $agence_id = '';

    protected $listeners = ['refreshAgents' => '$refresh'];

    public function toggleStatut(int $agentId, AgentService $service)
    {
        $agent = Agent::findOrFail($agentId);

        $nouveauStatut = $agent->statut === 'actif' ? 'inactif' : 'actif';

        $service->update(
            $agent->id,
            $agent->agence_id,
            $nouveauStatut,
            $agent->membre->user->roles->pluck('name')->toArray()
        );

        session()->flash('success', 'Statut de l’agent mis à jour');
        $this->dispatch('refreshAgents');
    }

    public function render()
    {
        $agents = Agent::with(['membre.user', 'agence'])
            ->when($this->search, function ($q) {
                $q->whereHas('membre.user', fn ($u) =>
                    $u->where('name', 'like', "%{$this->search}%")
                );
            })
            ->when($this->statut, fn ($q) =>
                $q->where('statut', $this->statut)
            )
            ->when($this->agence_id, fn ($q) =>
                $q->where('agence_id', $this->agence_id)
            )
            ->latest()
            ->paginate(10);

        return view('livewire.agents.agent-list', compact('agents'));
    }
}
