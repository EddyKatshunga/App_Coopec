<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\Agent;
use App\Models\HistoriqueRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ZoneService
{
    /**
     * Gère la création ou la mise à jour d'une zone avec gestion des rôles.
     */
    public function saveZone(array $data, ?Zone $zone = null): Zone
    {
        return DB::transaction(function () use ($data, $zone) {
            $nouveauChef = Agent::findOrFail($data['gerant_id']);
            $roleAgentCredit = Role::where('name', 'agent_credit')->first();
            $roleAgentEpargne = Role::where('name', 'agent_epargne')->first();

            // 1. Gestion de l'ancien chef (en cas d'édition)
            if ($zone && $zone->gerant_id !== $nouveauChef->id) {
                $ancienChef = $zone->gerant;
                if ($ancienChef && $ancienChef->user) {
                    $this->updateUserRole($ancienChef->user, 'agent_epargne');
                }
            }

            // 2. Mise à jour ou Création de la Zone
            if ($zone) {
                $zone->update($data);
            } else {
                $zone = Zone::create($data);
            }

            // 3. Assigner le rôle au nouveau chef
            if ($nouveauChef->user) {
                $this->updateUserRole($nouveauChef->user, 'agent_credit');
            }

            return $zone;
        });
    }

    /**
     * Met à jour le rôle de l'utilisateur et log le changement.
     */
    protected function updateUserRole($user, string $roleName): void
    {
        $nouveauRole = Role::where('name', $roleName)->firstOrFail();
        
        // On récupère l'ID du premier rôle actuel pour l'historique
        $ancienRoleId = $user->roles->first()?->id;

        // Si le rôle est déjà le bon, on ne fait rien pour éviter des logs inutiles
        if ($ancienRoleId === $nouveauRole->id) {
            return;
        }

        // SyncRoles remplace tous les rôles actuels par le nouveau
        $user->syncRoles([$roleName]);

        // Historisation
        HistoriqueRole::logRoleChange($user->id, $ancienRoleId, $nouveauRole->id);
    }
}