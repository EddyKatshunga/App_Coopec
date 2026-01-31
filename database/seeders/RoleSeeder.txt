<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Membre;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Définir tous les rôles de la coopérative
        $roles = [
            'membre',          // simple membre
            'agent_guichet',   // agent au guichet
            'agent_credit',    // agent crédit
            'agent_epargne',   // agent épargne
            'chef_agence',     // responsable agence
            'superviseur',     // superviseur interne
            'informaticien',   // support IT
            'ops',             // opérateur de saisie
            'caissier',        // gestion caisse
            'pca',             // président du conseil d'administration
            'conseiller',      // conseiller
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2️⃣ Créer un administrateur initial
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@coopec.test'],
            [
                'name' => 'Administrateur Principal',
                'password' => Hash::make('password123'),
            ]
        );

        $adminUser->assignRole('administrateur');

        // Créer le membre correspondant
        $adminMembre = Membre::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'numero_identification' => 'Z999555',
                'qualite' => 'Auxiliaire',
                'sexe' => 'M',
                'lieu_de_naissance' => 'Kikwit',
                'date_de_naissance' => '1980-01-01',
                'adresse' => '123 Rue Principale, Ville Principale',
                'telephone' => '0123456789',
                'activites' => 'Administration',
                'adresse_activite' => '123 Rue Principale, Ville Principale',
                'date_adhesion' => now(),
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]
        );

        // 3️⃣ Créer un agent test
        $agentUser = User::firstOrCreate(
            ['email' => 'agent@coopec.test'],
            [
                'name' => 'Agent Test',
                'password' => Hash::make('password123'),
            ]
        );

        $agentUser->assignRole('agent_guichet');

        $agentMembre = Membre::firstOrCreate(
            ['user_id' => $agentUser->id],
            [
                'numero_identification' => 'Z999444',
                'qualite' => 'Auxiliaire',
                'sexe' => 'M',
                'lieu_de_naissance' => 'Kikwit',
                'date_de_naissance' => '1980-01-01',
                'adresse' => '123 Rue Principale, Ville Principale',
                'telephone' => '0123456789',
                'activites' => 'Administration',
                'adresse_activite' => '123 Rue Principale, Ville Principale',
                'date_adhesion' => now(),
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]
        );

        Agent::firstOrCreate(
            ['membre_id' => $agentMembre->id],
            [
                'agence_id' => null,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]
        );
    }
}
