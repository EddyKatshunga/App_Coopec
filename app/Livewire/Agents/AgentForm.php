<?php

namespace App\Livewire\Agents;

use Livewire\Component;
use App\Models\Agent;
use App\Models\Membre;
use App\Models\Agence;
use App\Services\AgentService;
use Spatie\Permission\Models\Role;

class AgentForm extends Component
{
    public ?Agent $agent = null;

    public $membre_id;
    public $agence_id;
    public $statut = 'actif';
    public $role = ''; // Une seule propriété pour le rôle

    public function mount(?Agent $agent = null)
    {
        if ($agent) {
            $this->agent = $agent;
            $this->membre_id = $agent->membre_id;
            $this->agence_id = $agent->agence_id;
            $this->statut = $agent->statut;
            // Pour la modification, on prend le premier rôle
            $this->role = $agent->membre->user->roles->pluck('name')->first() ?? '';
        }
    }

    protected function rules()
    {
        return [
            'membre_id' => 'required|exists:membres,id',
            'agence_id' => 'required|exists:agences,id',
            'statut' => 'required|in:actif,inactif',
            'role' => 'required|string|exists:roles,name',
        ];
    }

    public function save(AgentService $service)
    {
        $data = $this->validate();

        if ($this->agent) {
            $service->update(
                $this->agent->id,
                $this->agence_id,
                $this->statut,
                $this->role
            );
            session()->flash('success', 'Agent modifié avec succès');
        } else {
            $service->promote(
                $this->membre_id,
                $this->agence_id,
                $this->statut,
                $this->role
            );
            session()->flash('success', 'Agent créé avec succès');
        }

        return redirect()->route('agents.index');
    }

    public function render()
    {
        return view('livewire.agents.agent-form', [
            'membres' => Membre::with('user')->whereDoesntHave('agent')->get(),
            'agences' => Agence::all(),
            'rolesDisponibles' => Role::pluck('name'),
        ])->layout('layouts.app');
    }
}