<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisation de updateOrInsert pour le taux pour éviter les doublons sur la date
        DB::table('taux_changes')->updateOrInsert(
            ['date_application' => now()->format('Y-m-d')],
            [
                'uuid' => (string) Str::uuid(),
                'taux_achat' => 2300.0000,
                'taux_vente' => 2500.0000,
                'est_actif' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 1. COMPTES SPÉCIFIQUES & DEVISES
        $accounts = [
            // Classe 1
            ['numero' => '10', 'nom' => 'Capital social', 'type' => 'passif'],
            // Classe 1 (capitaux)
            ['numero' => '12', 'nom' => 'Résultat de l\'exercice', 'type' => 'passif'],
            ['numero' => '13', 'nom' => 'Report à nouveau', 'type' => 'passif'],
            ['numero' => '16', 'nom' => 'Emprunt à long terme', 'type' => 'passif'],
            
            // Classe 2
            ['numero' => '21', 'nom' => 'Matériel et mobilier', 'type' => 'actif'],
            ['numero' => '22', 'nom' => 'Terrain', 'type' => 'actif'],
            ['numero' => '23', 'nom' => 'Amortissements des immobilisations', 'type' => 'actif'],

            // Classe 4 (Tiers)

            // Dettes, englobe pour sousci de simplification tout type de dettes 
            //(charges dûes, un achat à crédit...)
            ['numero' => '40', 'nom' => 'Dettes diverses', 'type' => 'passif'],

            //Dette particulière étant donné les objectifs d'une Microfinance
            //Gestion des opérations de dépots et retrait Epargnes en CDF et USD
            ['numero' => '41', 'nom' => 'Épargne membres', 'type' => 'passif'],

            // Créances, englobe tout type de créance
            ['numero' => '45', 'nom' => 'Créances diverses', 'type' => 'actif'],

            //Créances particulières étant donné les objectifs d'une Microfinance
            //Lors de l'octroi d'un crédit, ces deux créances sont crées
            ['numero' => '46', 'nom' => 'Capital restant dû', 'type' => 'actif'],
            ['numero' => '47', 'nom' => 'Intérêt à percevoir', 'type' => 'actif'],


            // Classe 5 (Trésorerie)
            ['numero' => '52', 'nom' => 'Banque', 'type' => 'actif'],
            ['numero' => '57', 'nom' => 'Caisse', 'type' => 'actif'], 
            ['numero' => '58', 'nom' => 'Virements internes', 'type' => 'actif'],

            // Classe 6 (Charges)
            ['numero' => '60', 'nom' => 'Fournitures et consommables de bureau', 'type' => 'charge'],
            ['numero' => '61', 'nom' => 'Loyers et Entretien', 'type' => 'charge'],
            ['numero' => '62', 'nom' => 'Eau & Electricité', 'type' => 'charge'],
            ['numero' => '621', 'nom' => 'Frais de Transport', 'type' => 'charge'],
            ['numero' => '622', 'nom' => 'Internet et communication', 'type' => 'charge'],
            ['numero' => '63', 'nom' => 'Salaire', 'type' => 'charge'],
            ['numero' => '64', 'nom' => 'Impots et Taxes', 'type' => 'charge'],
            ['numero' => '65', 'nom' => 'Frais Honoraires et Autres services', 'type' => 'charge'],
            ['numero' => '651', 'nom' => 'Frais Hebergement et Maintenance Système', 'type' => 'charge'],
            ['numero' => '66', 'nom' => 'Intérets et penalités à payer', 'type' => 'charge'], // Cas où c'est la COOPEC qui souscrit à un pret
            ['numero' => '67', 'nom' => 'Pertes de caisse et de change', 'type' => 'charge'],
            ['numero' => '68', 'nom' => 'Amortissement', 'type' => 'charge'],
            ['numero' => '69', 'nom' => 'Dividendes des associés', 'type' => 'charge'],
            ['numero' => '699', 'nom' => 'Autres Charges', 'type' => 'charge'],

            // Classe 7 (Produits)
            ['numero' => '70', 'nom' => 'Frais d\'adhésion', 'type' => 'produit'],
            ['numero' => '71', 'nom' => 'Intérêts perçus sur crédits', 'type' => 'produit'],
            ['numero' => '72', 'nom' => 'Vente carnets', 'type' => 'produit'],
            ['numero' => '73', 'nom' => 'Frais Dossiers Crédit', 'type' => 'produit'],
            ['numero' => '74', 'nom' => 'Pénalités', 'type' => 'produit'],
            ['numero' => '75', 'nom' => 'Dons et subventions', 'type' => 'produit'],
            ['numero' => '76', 'nom' => 'Bonis de caisse et de change', 'type' => 'produit'],
            ['numero' => '799', 'nom' => 'Autres Produits', 'type' => 'produit'],
        ];

        foreach ($accounts as $acc) {
            // Cherche par numéro, si non trouvé, crée avec toutes les infos
            Account::updateOrCreate(
                ['numero' => $acc['numero']], 
                [
                    'uuid' => (string) Str::uuid(),
                    'nom' => $acc['nom'],
                    'type' => $acc['type'],
                ]
            );
        }
    }
}