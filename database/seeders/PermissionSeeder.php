<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ============================================================
         * 1️⃣ PERMISSIONS MÉTIER ATOMIQUES
         * ============================================================
         */
        $permissions = [
            // ----- Membre (Le socle commun à tous les utilisateurs) -----
            'can.level0', //Membre simple
            'can.level1', //Gestion dépots Epargne
            'can.level2', //Gestion Crédit & Remboursements
            'can.level3', //Gestion Retrait, Revenu, Depense, Caisse
            'can.level4', //Gestion des membres, Superviseur de l'agence
            'can.level5', //Gestion de l'agence, chef d'Agence
            'can.level6', //Gestion de plusieurs agences, direction générale
            'can.level7', //Administration générale
            'can.level8', //Super-admin
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * ============================================================
         * 2️⃣ CONSTRUCTION DES CASCADES (HIÉRARCHIE)
         * ============================================================
         */

        // 🟢 Niveau 0 (Tout utilisateur est au moins un membre)
        $niveau0Permissions = [
            'can.level0',
        ];

        // 🧾 Niveau 1
        $niveau1Permissions = [
            'can.level0', 'can.level1',   
        ];

        // 🧾 Niveau 2
        $niveau2Permissions = [
            'can.level0', 'can.level1', 'can.level2',
        ];

        // 🧾 Niveau 3
        $niveau3Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 
        ];

        // 🧾 Niveau 4
        $niveau4Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 'can.level4',
        ];

        // 🧾 Niveau 5
        $niveau5Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 'can.level4', 'can.level5',
        ];

        // 🧾 Niveau 6
        $niveau6Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 'can.level4', 'can.level5', 'can.level6',
        ];

        // 🧾 Niveau 7
        $niveau7Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 'can.level4', 'can.level5', 'can.level6', 'can.level7',
        ];

        // 🧾 Niveau 8
        $niveau8Permissions = [
            'can.level0', 'can.level1', 'can.level2', 'can.level3', 'can.level4', 'can.level5', 'can.level6', 'can.level7', 'can.level8',
        ];


        /**
         * ============================================================
         * 3️⃣ CRÉATION ET SYNCHRONISATION DES RÔLES
         * ============================================================
         */
        $rolesConfig = [
            'niveau 0' => $niveau0Permissions,
            'niveau 1' => $niveau1Permissions,
            'niveau 2' => $niveau2Permissions,
            'niveau 3' => $niveau3Permissions,
            'niveau 4' => $niveau4Permissions,
            'niveau 5' => $niveau5Permissions,
            'niveau 6' => $niveau6Permissions,
            'niveau 7' => $niveau7Permissions,
            'niveau 8' => $niveau8Permissions,
        ];

        foreach ($rolesConfig as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }
    }
}