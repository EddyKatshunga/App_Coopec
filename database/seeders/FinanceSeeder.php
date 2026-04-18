<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // à adapter si besoin

        /**
         * ===========================
         * TYPES DE DEPENSES (RDC Microfinance)
         * ===========================
         */
        $typesDepenses = [
            ['nom' => 'Salaires du personnel', 'code_comptable' => '61'],
            ['nom' => 'Loyer des bureaux', 'code_comptable' => '62'],
            ['nom' => 'Fournitures de bureau', 'code_comptable' => '63'],
            ['nom' => 'Frais de transport', 'code_comptable' => '64'],
            ['nom' => 'Entretien et maintenance', 'code_comptable' => '65'],
            ['nom' => 'Charges informatiques', 'code_comptable' => '66'],
            ['nom' => 'Frais de communication', 'code_comptable' => '67'],
            ['nom' => 'Charges bancaires', 'code_comptable' => '68'],
            ['nom' => 'Pertes sur crédits', 'code_comptable' => '69'],
            ['nom' => 'Autres charges opérationnelles', 'code_comptable' => '60'],
        ];

        foreach ($typesDepenses as $depense) {
            DB::table('types_depenses')->updateOrInsert(
                ['nom' => $depense['nom']],
                [
                    'uuid' => (string) Str::ulid(),
                    'code_comptable' => $depense['code_comptable'],
                    'est_actif' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /**
         * ===========================
         * TYPES DE REVENUS (RDC Microfinance)
         * ===========================
         */
        $typesRevenus = [
            ['nom' => 'Report à nouveau', 'code_comptable' => '110'],
            ['nom' => 'Frais de dossier', 'code_comptable' => '71'],
            ['nom' => 'Pénalités de retard', 'code_comptable' => '72'],
            ['nom' => 'Commissions diverses', 'code_comptable' => '73'],
            ['nom' => 'Vente de carnets', 'code_comptable' => '74'],
            ['nom' => 'Frais d’adhésion', 'code_comptable' => '75'],
            ['nom' => 'Subventions et dons', 'code_comptable' => '76'],
            ['nom' => 'Produits financiers', 'code_comptable' => '77'],
            ['nom' => 'Récupération de créances', 'code_comptable' => '78'],
            ['nom' => 'Autres produits', 'code_comptable' => '79'],
        ];

        foreach ($typesRevenus as $revenu) {
            DB::table('types_revenus')->updateOrInsert(
                ['nom' => $revenu['nom']],
                [
                    'uuid' => (string) Str::ulid(),
                    'code_comptable' => $revenu['code_comptable'],
                    'est_actif' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /**
         * ===========================
         * AGENCE
         * ===========================
         */
        DB::table('agences')->updateOrInsert(
            ['nom' => 'Agence Principale Kikwit'],
            [
                'uuid' => (string) Str::ulid(),
                'code' => 'KKT-001',
                'ville' => 'Kikwit',
                'pays' => 'RDC',
                'solde_actuel_epargne_cdf' => 1726000,
                'solde_actuel_epargne_usd' => 1496,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}