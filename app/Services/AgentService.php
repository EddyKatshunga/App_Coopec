<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Membre;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AgentService
{
    /**
     * Promouvoir un membre en agent
     */
    public function promote(
        int $membreId,
        int $agenceId,
        array $roles = []
    ): Agent {
        return DB::transaction(function () use ($membreId, $agenceId, $roles) {

            $membre = Membre::with('user')->findOrFail($membreId);

            if (! $membre->user) {
                throw new ModelNotFoundException(
                    "Le membre n'a pas de compte utilisateur."
                );
            }

            // Créer ou récupérer l’agent
            $agent = Agent::firstOrCreate(
                ['membre_id' => $membre->id],
                [
                    'agence_id' => $agenceId,
                    'statut' => 'actif',
                ]
            );

            // Mise à jour agence + statut
            $agent->update([
                'agence_id' => $agenceId,
                'statut' => 'actif',
            ]);

            // Rôles Spatie (cumul autorisé)
            if (! empty($roles)) {
                $membre->user->syncRoles($roles);
            }

            return $agent;
        });
    }

    /**
     * Mettre à jour un agent
     */
    public function update(
        int $agentId,
        int $agenceId,
        string $statut,
        array $roles = []
    ): Agent {
        return DB::transaction(function () use ($agentId, $agenceId, $statut, $roles) {

            $agent = Agent::with('membre.user')->findOrFail($agentId);
            $user = $agent->membre->user;

            $agent->update([
                'agence_id' => $agenceId,
                'statut' => $statut,
            ]);

            if (! empty($roles)) {
                $user->syncRoles($roles);
            }

            // Si inactif → redevenir membre simple
            if ($statut === 'inactif') {
                $user->syncRoles(['membre']);
            }

            return $agent;
        });
    }
}
