<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Membre;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AgentService
{
    /**
     * Créer ou mettre à jour un agent (méthode unifiée)
     */
    public function saveAgent(
        ?int $agentId = null,
        int $membreId,
        int $agenceId,
        string $statut,
        string $role
    ): Agent {
        return DB::transaction(function () use ($agentId, $membreId, $agenceId, $statut, $role) {

            $membre = Membre::with('user')->findOrFail($membreId);

            if (! $membre->user) {
                throw new ModelNotFoundException(
                    "Le membre n'a pas de compte utilisateur."
                );
            }

            // Si on a un agentId, c'est une modification
            if ($agentId) {
                $agent = Agent::with('membre.user')->findOrFail($agentId);
                
                // Vérifier que l'agent appartient bien au membre
                if ($agent->membre_id !== $membreId) {
                    throw new \Exception("L'agent n'appartient pas au membre spécifié.");
                }
                
                $agent->update([
                    'agence_id' => $agenceId,
                    'statut' => $statut,
                ]);
                
                $user = $agent->membre->user;
            } 
            // Sinon, c'est une création
            else {
                // Vérifier qu'un agent n'existe pas déjà pour ce membre
                $existingAgent = Agent::where('membre_id', $membreId)->first();
                
                if ($existingAgent) {
                    // Option 1: Lever une exception
                    throw new \Exception("Un agent existe déjà pour ce membre.");
                    
                    // Option 2: Mettre à jour l'existant
                    // $existingAgent->update([
                    //     'agence_id' => $agenceId,
                    //     'statut' => $statut,
                    // ]);
                    // $user = $membre->user;
                    // return $existingAgent;
                }
                
                $agent = Agent::create([
                    'membre_id' => $membreId,
                    'agence_id' => $agenceId,
                    'statut' => $statut,
                ]);
                
                $user = $membre->user;
            }

            // Assigner le rôle unique
            $user->syncRoles([$role]);

            return $agent;
        });
    }

    /**
     * Promouvoir un membre en agent (création uniquement)
     */
    public function promote(
        int $membreId,
        int $agenceId,
        string $statut,
        string $role
    ): Agent {
        return $this->saveAgent(null, $membreId, $agenceId, $statut, $role);
    }

    /**
     * Mettre à jour un agent (modification uniquement)
     */
    public function update(
        int $agentId,
        int $agenceId,
        string $statut,
        string $role
    ): Agent {
        $agent = Agent::findOrFail($agentId);
        return $this->saveAgent($agentId, $agent->membre_id, $agenceId, $statut, $role);
    }
}