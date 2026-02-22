<?php

namespace App\Livewire\Agents;

use Livewire\Component;
use App\Models\Agent;
use App\Models\Membre;
use App\Models\Agence;
use App\Services\AgentService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class AgentForm extends Component
{
    public ?Agent $agent = null;
    public ?Membre $membre = null;

    public $membre_id;
    public $user_id;
    public $agence_id;
    public $role_name = ''; // Nom pour Spatie
    public $role_id = '';   // ID pour l'historique

    // Liste des rôles autorisés à l'affichage
    protected $allowedRoles = [
        'agent_epargne', 'superviseur', 'ops', 
        'conseiller', 'caissiere', 'auditeur'
    ];

    public function mount(?Membre $membre = null, ?Agent $agent = null)
    {
        if ($agent && $agent->exists) {
            $this->agent = $agent;
            $this->membre = $agent->membre;
            $this->membre_id = $agent->membre_id;
            $this->user_id = $agent->user_id;
            $this->agence_id = $agent->agence_id;
            
            $currentRole = $agent->user->roles->first();
            if ($currentRole) {
                $this->role_name = $currentRole->name;
                $this->role_id = $currentRole->id;
            }
        } elseif ($membre && $membre->exists) {
            $this->membre = $membre;
            $this->membre_id = $membre->id;
            $this->user_id = $membre->user_id; 
        } else {
            abort(403, 'Contexte invalide.');
        }
    }

    /**
     * Dès que le rôle change dans le comboBox, on met à jour l'ID du rôle
     */
    public function updatedRoleName($name)
    {
        $role = Role::where('name', $name)->first();
        $this->role_id = $role ? $role->id : null;
    }

    protected function rules()
    {
        return [
            'membre_id' => 'required|exists:membres,id',
            'user_id'   => 'required|exists:users,id',
            'agence_id' => 'required|exists:agences,id',
            'role_name' => 'required|in:' . implode(',', $this->allowedRoles),
            'role_id'   => 'required|exists:roles,id',
        ];
    }

    public function save(AgentService $service)
    {
        $this->validate();

        try {
            if ($this->agent) {
                $service->updateAgent(
                    $this->agent->id,
                    (int) $this->agence_id,
                    $this->role_name,
                    (int) $this->role_id
                );
                session()->flash('success', 'Agent mis à jour.');
            } else {
                $service->createAgent(
                    (int) $this->membre_id,
                    (int) $this->user_id,
                    (int) $this->agence_id,
                    $this->role_name,
                    (int) $this->role_id
                );
                session()->flash('success', 'Agent créé avec succès.');
            }
            return redirect()->route('agents.index');
        } catch (\Exception $e) {
            // Capture l'erreur du service (ex: blocage modification agence)
            $this->addError('agence_id', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agents.agent-form', [
            'agences' => Agence::all(),
            'rolesDisponibles' => Role::whereIn('name', $this->allowedRoles)->get(),
        ])->layout('layouts.app');
    }
}