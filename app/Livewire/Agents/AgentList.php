<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AgentList extends Component
{
    use WithPagination;

    public $search = '';
    public $agence_id = null;

    public function mount()
    {
        // Par défaut, l'agence de l'utilisateur connecté
        $this->agence_id = auth()->user()->agence_id ?? null;
    }

    public function render()
    {
        // Sécurité : seul le level6 peut changer d'agence, sinon on force l'agence de l'user
        $currentAgenceId = auth()->user()->can('can.level6') 
            ? $this->agence_id 
            : auth()->user()->agence_id;

        $agents = Agent::with(['membre.user.roles', 'agence'])
            ->where('agence_id', $currentAgenceId)
            ->when($this->search, function ($q) {
                $q->whereHas('membre.user', fn ($u) =>
                    $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                );
            })
            ->latest()
            ->paginate(12);

        $agences = auth()->user()->can('can.level6') 
            ? Agence::orderBy('nom')->get() 
            : collect();

        return view('livewire.agents.agent-list', [
            'agents' => $agents,
            'agences' => $agences
        ]);
    }
}