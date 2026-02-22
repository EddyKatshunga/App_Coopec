<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\User;
use App\Models\HistoriqueRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AgentService
{
    /**
     * Création d'un agent
     */
    public function createAgent(int $membreId, int $userId, int $agenceId, string $roleName, int $roleId): Agent
    {
        return DB::transaction(function () use ($membreId, $userId, $agenceId, $roleName, $roleId) {
            
            // 1. Insertion dans le modèle Agent
            $agent = Agent::create([
                'membre_id' => $membreId,
                'user_id'   => $userId,
                'agence_id' => $agenceId,
            ]);

            $user = User::findOrFail($userId);

            // 2. Récupérer l'ancien rôle (le premier car un seul autorisé)
            $oldRole = $user->roles->first();
            $oldRoleId = $oldRole ? $oldRole->id : null;

            // 3. Assigner le nouveau rôle (écrase les anciens via syncRoles)
            $user->syncRoles([$roleName]);

            // 4. Insérer dans HistoriqueRole
            HistoriqueRole::create([
                'user_id'      => $userId,
                'nouveau_role' => $roleId,
                'ancien_role'  => $oldRoleId,
            ]);

            return $agent;
        });
    }

    public function changerRole(int $userId, string $oldRole, string $newRole): void
    {
        $user = User::findOrFail($userId);
        $user->syncRoles([$oldRole]);

        $oldRole = \Spatie\Permission\Models\Role::findByName($oldRole);
        $oldRoleId = $oldRole->id;

        $newRole = \Spatie\Permission\Models\Role::findByName($newRole);
        $newRoleId = $newRole->id;

        HistoriqueRole::create([
            'user_id'      => $user->id,
            'nouveau_role' => $newRoleId,
            'ancien_role'  => $oldRoleId,
        ]);
    }
    /**
     * Modification d'un agent
     */
    public function updateAgent(int $agentId, int $agenceId, string $roleName, int $roleId): Agent
    {
        return DB::transaction(function () use ($agentId, $agenceId, $roleName, $roleId) {
            
            $agent = Agent::with('user')->findOrFail($agentId);
            $user = $agent->user;

            // --- Logique de restriction sur l'agence ---
            // On récupère le rôle actuel avant modification
            $currentRoleName = $user->getRoleNames()->first();

            if (in_array($currentRoleName, ['chef_agence', 'agent_credit'])) {
                // Si le rôle actuel est sensible, on vérifie si l'agence tente d'être changée
                throw new \Exception("Impossible de modifier l'agence pour un agent ayant le rôle : " . $currentRoleName);
            }

            // Mise à jour de l'agence
            $agent->update([
                'agence_id' => $agenceId,
            ]);

            // --- Gestion du rôle et de l'historique ---
            $oldRole = $user->roles->first();
            $oldRoleId = $oldRole ? $oldRole->id : null;

            // On ne procède à la mise à jour que si le rôle a changé
            if ($oldRoleId !== $roleId) {
                $user->syncRoles([$roleName]);

                HistoriqueRole::create([
                    'user_id'      => $user->id,
                    'nouveau_role' => $roleId,
                    'ancien_role'  => $oldRoleId,
                ]);
            }

            return $agent;
        });
    }
}