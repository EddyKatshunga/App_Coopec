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
            'membre.view.profile',      // Accès à ses propres informations
            'membre.view.epargne',     // Consulter ses soldes
            'membre.view.prets',       // Consulter l'état de ses crédits
            'membre.view.remboursements',
            'membre.signal.problem',    // Signaler une anomalie au support
            'membre.change.password',   // Sécurité personnelle

            // ----- Gestion Administrative des Membres -----
            'membre.create',           // Recrutement de nouveaux clients
            'membre.update',           // Mise à jour des dossiers clients

            // ----- Épargne (Mouvements de fonds) -----
            'epargne.depot.create',    // Enregistrement d'un dépôt (Agent/OPS)
            'epargne.retrait.create',  // Décaissement d'espèces (Caisse uniquement)
            'epargne.view.transactions',
            'epargne.view.my_depots',  // Suivi de collecte pour les agents
            'epargne.correct',         // Annulation/Extourne (Haut risque)

            // ----- Crédit (Cycle de vie du prêt) -----
            'credit.pret.create',      // Montage du dossier
            'credit.pret.view',
            'credit.pret.valider',     // Approbation technique/comptable
            'credit.pret.decaisser',   // Sortie physique des fonds (Caisse)
            'credit.remboursement.create',
            'credit.remboursement.view',
            'credit.remboursement.correct',
            'credit.cloturer',         // Clôture administrative du prêt

            // ----- Comptabilité & Finances -----
            'depense.create',          // Enregistrement d'une charge
            'depense.view',
            'depense.valider',         // Accord pour le paiement de la charge
            'compta.cloture.view',     // Lecture des journaux de clôture
            'compta.rapports.view',    // Bilans et rapports financiers
            'compta.audit.logs',       // Surveillance des actions utilisateurs

            // ----- Structure & Agences -----
            'agence.operations.view', //Les opérations d'une agence
            'agent.create',            // Création de comptes employés
            'agent.assign.role',       // Modification des privilèges
            'zone.create',
            'zone.update',
            'zone.view',
            'agence.view.all',         // Vision multi-agences
            'agence.manage.all',       // Paramètres globaux du système
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * ============================================================
         * 2️⃣ CONSTRUCTION DES CASCADES (HIÉRARCHIE)
         * ============================================================
         */

        // 🟢 LE SOCLE : MEMBRE (Tout utilisateur est au moins un membre)
        $membrePermissions = [
            'membre.view.profile', 'membre.view.epargne', 'membre.view.prets',
            'membre.view.remboursements', 'membre.signal.problem', 'membre.change.password',
        ];

        // 👁️ AUDITEUR / CONSEILLER (Lecture seule étendue + Droits membre)
        $auditeurPermissions = array_merge($membrePermissions, [
            'epargne.view.transactions', 'credit.pret.view', 'credit.remboursement.view',
            'depense.view', 'zone.view', 'agence.view.all', 'compta.cloture.view',
            'compta.rapports.view',
        ]);

        // ⌨️ OPS (Opérateur de Saisie + Droits membre)
        // Focus sur la saisie rapide sans pouvoir de validation.
        $opsPermissions = array_merge($membrePermissions, [
            'epargne.depot.create', 'credit.pret.create', 'credit.remboursement.create',
            'membre.create', 'depense.create',
        ]);

        // 🧾 AGENT ÉPARGNE (Terrain)
        $agentEpargnePermissions = array_merge($membrePermissions, [
            'epargne.depot.create', 'epargne.view.my_depots',
        ]);

        // 💳 AGENT CRÉDIT (Analyse)
        $agentCreditPermissions = array_merge($agentEpargnePermissions, [
            'credit.pret.create', 'credit.pret.view', 'credit.remboursement.view',
        ]);

        // 💰 CAISSIÈRE (Manipulation Cash)
        // Note : Elle ne valide pas le crédit, elle décaisse ce qui est validé.
        $caissierePermissions = array_merge($membrePermissions, [
            'epargne.depot.create', 'epargne.retrait.create', 'epargne.view.transactions',
            'credit.remboursement.create', 'credit.pret.decaisser', 'depense.view',
        ]);

        // 📊 COMPTABLE (Le Verrou du système + Droits membre)
        // Il peut tout voir et doit valider les flux avant décaissement.
        $comptablePermissions = array_merge($auditeurPermissions, [
            'depense.create', 'depense.valider', 'credit.pret.valider', 
            'compta.audit.logs', 'membre.update',
        ]);

        // 🧠 SUPERVISEUR (Opérationnel local)
        $superviseurPermissions = array_merge($caissierePermissions, $agentCreditPermissions, [
            'epargne.correct', 'credit.remboursement.correct', 'membre.create',
            'depense.create', 'credit.cloturer', 'agence.operations.view',
        ]);

        // 🏢 CHEF D’AGENCE (Autorité locale maximale)
        $chefAgencePermissions = array_merge($superviseurPermissions, [
            'agent.create', 'agent.assign.role', 'zone.create', 'zone.update',
            'credit.pret.valider',
        ]);

        // 🌍 ACCÈS TOTAL
        $fullPermissions = Permission::all()->pluck('name')->toArray();

        /**
         * ============================================================
         * 3️⃣ CRÉATION ET SYNCHRONISATION DES RÔLES
         * ============================================================
         */
        $rolesConfig = [
            'membre'               => $membrePermissions,
            'ops'                  => $opsPermissions,
            'auditeur'             => $auditeurPermissions,
            'conseiller'           => $auditeurPermissions,
            'comptable'            => $comptablePermissions,
            'agent_epargne'        => $agentEpargnePermissions,
            'agent_credit'         => $agentCreditPermissions,
            'caissiere'            => $caissierePermissions,
            'superviseur'          => $superviseurPermissions,
            'chef_agence'          => $chefAgencePermissions,
            'administrateur'       => $fullPermissions,
            'directrice_regionale' => $fullPermissions,
            'pca'                  => $fullPermissions,
        ];

        foreach ($rolesConfig as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }
    }
}