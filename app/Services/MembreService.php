<?php

namespace App\Services;

use App\Models\User;
use App\Models\Membre;
use App\Models\Agent;
use App\Models\Compte;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class MembreService
{
    public function getFicheData(Membre $membre): array
    {
        $membre->load([
            'user',
            'agent',
            'agentParrain',
            'comptes'
        ]);

        return [
            'membre' => $membre,
            'total_cdf' => $membre->comptes->sum('solde_cdf'),
            'total_usd' => $membre->comptes->sum('solde_usd'),
            'generated_at' => now(),
        ];
    }
    /**
     * Crée un membre avec un utilisateur associé.
     *
     * @param array $data
     * @return Membre
     */

    public function createMembre(array $data): Membre
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['nom_complet'] ?? $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('membre');

            $membre = Membre::create([
                'user_id' => $user->id,
                'numero_identification' => $data['numero_identification'],
                'qualite' => $data['qualite'] ?? 'Auxiliaire',
                'sexe' => $data['sexe'],
                'lieu_de_naissance' => $data['lieu_de_naissance'],
                'date_de_naissance' => $data['date_de_naissance'],
                'adresse' => $data['adresse'] ?? 'RDC',
                'telephone' => $data['telephone'] ?? null,
                'activites' => $data['activites'] ?? 'Vente divers',
                'adresse_activite' => $data['adresse_activite'] ?? 'Kikwit',
                'date_adhesion' => $data['date_adhesion'] ?? now(),
            ]);

            $numero_compte = $data['numero_identification'] . '-00';
            Compte::create([
                'membre_id' => $membre->id,
                'numero_compte' => $numero_compte,
            ]);

            return $membre;
        });
    }


    /**
     * Met à jour les informations d'un membre.
     *
     * @param int $membreId
     * @param array $data
     * @return Membre
     * @throws ModelNotFoundException
     */
    public function updateMembre(int $membreId, array $data): Membre
    {
        $membre = Membre::findOrFail($membreId);
        $user = $membre->user;

        if (! $user) {
            throw new ModelNotFoundException("Le membre n'a pas de User associé.");
        }

        // User
        // User - PRIORITÉ au champ 'nom_complet' si présent
        $userData = [];
        
        // Utilise 'nom_complet' en priorité, sinon 'name'
        if (isset($data['nom_complet'])) {
            $userData['name'] = $data['nom_complet'];
        } elseif (isset($data['name'])) {
            $userData['name'] = $data['name'];
        }
        
        // Ajoute email si présent
        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }
        
        // Ajoute password si présent
        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        // Membre
        $membreData = array_intersect_key($data, array_flip([
            'numero_identification',
            'qualite',
            'sexe',
            'lieu_de_naissance',
            'date_de_naissance',
            'adresse',
            'telephone',
            'activites',
            'adresse_activite',
            'date_adhesion',
            'agent_parrain_id',
        ]));

        $membre->update($membreData);

        return $membre;
    }

}
