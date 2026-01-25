<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Définir les permissions métier
        $permissions = [
            // Membres
            'membre.creer',
            'membre.modifier',
            'membre.supprimer',
            'membre.consulter',

            // Agents
            'agent.creer',
            'agent.modifier',

            // Épargne
            'epargne.depot',
            'epargne.retrait',
            'epargne.consulter',

            // Crédit
            'credit.demande',
            'credit.approuver',
            'credit.rejeter',

            // Administration
            'utilisateur.gerer',
            'roles.permissions.gerer',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2️⃣ Lier permissions ↔ rôles
        Role::findByName('membre')->givePermissionTo([
            'membre.consulter',
            'epargne.consulter',
        ]);

        Role::findByName('agent_guichet')->givePermissionTo([
            'epargne.depot',
            'epargne.retrait',
            'membre.consulter',
        ]);

        Role::findByName('agent_credit')->givePermissionTo([
            'credit.demande',
            'credit.approuver',
            'credit.rejeter',
        ]);

        Role::findByName('administrateur')
            ->givePermissionTo(Permission::all());
    }
}
