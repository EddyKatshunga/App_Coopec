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
         * ===============================
         * 1️⃣ PERMISSIONS MÉTIER ATOMIQUES
         * ===============================
         */
        $permissions = [

            // ===== Membre (lecture + signalement) =====
            'membre.view.profile',
            'membre.view.epargne',
            'membre.view.prets',
            'membre.view.remboursements',
            'membre.signal.problem',
            'membre.change.password',

            // ===== Membres (gestion) =====
            'membre.create',
            'membre.update',

            // ===== Épargne =====
            'epargne.depot.create',
            'epargne.retrait.create',
            'epargne.view.transactions',
            'epargne.view.my_depots',
            'epargne.correct',

            // ===== Crédit =====
            'credit.pret.create',
            'credit.pret.view',
            'credit.remboursement.create',
            'credit.remboursement.view',
            'credit.remboursement.correct',
            'credit.cloturer',

            // ===== Dépenses =====
            'depense.create',
            'depense.view',

            // ===== Agents & rôles =====
            'agent.create',
            'agent.assign.role',

            // ===== Zone épargne =====
            'zone.create',
            'zone.update',
            'zone.view',

            // ===== Agences =====
            'agence.view.all',
            'agence.manage.all',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * ===============================
         * 2️⃣ CASCADE RÔLES → PERMISSIONS
         * ===============================
         */

        // 🧑‍💼 MEMBRE
        $membrePermissions = [
            'membre.view.profile',
            'membre.view.epargne',
            'membre.view.prets',
            'membre.view.remboursements',
            'membre.signal.problem',
            'membre.change.password',
        ];

        // 🧾 AGENT ÉPARGNE
        $agentEpargnePermissions = array_merge($membrePermissions, [
            'epargne.depot.create',
            'epargne.view.my_depots',
            'epargne.view.transactions',
            'membre.create',
        ]);

        // 💳 AGENT CRÉDIT
        $agentCreditPermissions = array_merge($agentEpargnePermissions, [
            'credit.remboursement.create',
            'credit.pret.view',
            'credit.remboursement.view',
        ]);

        // 💼 CAISSIÈRE
        $caissierePermissions = array_merge($agentCreditPermissions, [
            'epargne.retrait.create',
        ]);

        // 🧠 SUPERVISEUR
        $superviseurPermissions = array_merge($caissierePermissions, [
            'epargne.correct',
            'credit.remboursement.correct',
            'credit.pret.create',
            'credit.cloturer',
            'membre.update',
            'depense.create',
        ]);

        // 🏢 DIRECTEUR D’AGENCE
        $directeurAgencePermissions = array_merge($superviseurPermissions, [
            'agent.create',
            'agent.assign.role',
            'zone.create',
            'zone.update',
            'zone.view',
        ]);

        // 🧾 OPS (opérateur de saisie)
        $opsPermissions = [
            'epargne.depot.create',
            'credit.pret.create',
            'credit.remboursement.create',
            'membre.create',
            'depense.create',
        ];

        // 👁️ CONSEILLER / AUDITEUR (lecture seule)
        $auditeurPermissions = [
            'membre.view.profile',
            'membre.view.epargne',
            'membre.view.prets',
            'membre.view.remboursements',
            'epargne.view.transactions',
            'credit.pret.view',
            'credit.remboursement.view',
            'depense.view',
            'zone.view',
        ];

        // 🌍 DIRECTRICE RÉGIONALE & PCA (FULL ACCESS)
        $fullPermissions = Permission::all()->pluck('name')->toArray();

        /**
         * ===============================
         * 3️⃣ ATTRIBUTION AUX RÔLES
         * ===============================
         */
        $roles = [
            'membre' => $membrePermissions,
            'agent_epargne' => $agentEpargnePermissions,
            'agent_credit' => $agentCreditPermissions,
            'caissiere' => $caissierePermissions,
            'superviseur' => $superviseurPermissions,
            'chef_agence' => $directeurAgencePermissions,
            'ops' => $opsPermissions,
            'conseiller' => $auditeurPermissions,
            'auditeur' => $auditeurPermissions,
            'administrateur' => $fullPermissions,
            'directrice_regionale' => $fullPermissions,
            'pca' => $fullPermissions,
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
