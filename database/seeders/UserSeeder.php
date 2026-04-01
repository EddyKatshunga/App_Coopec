<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Membre;
use App\Models\Compte;
use App\Models\HistoriqueRole;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    protected function createUserWithRole(array $userData, string $roleName, array $membreData): void
    {
        DB::transaction(function () use ($userData, $roleName, $membreData) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            // Assigner le rôle
            $user->assignRole($roleName);
            // Créer le membre
            Membre::create(array_merge($membreData, ['user_id' => $user->id]));
        });
    }

    public function run(): void
    {
        // Créer l'admin niveau 7
        $this->createUserWithRole(
            userData: [
                'name' => 'Administrateur Général',
                'email' => 'admin@example.com',
                'password' => 'password123',
            ],
            roleName: 'niveau 7',
            membreData: [
                'numero_identification' => 'ADMIN-001',
                'qualite' => 'Auxiliaire',
                'sexe' => 'M',
                'lieu_de_naissance' => 'Kinshasa',
                'date_de_naissance' => '1990-01-01',
                'adresse' => 'Gombe, Kinshasa',
                'telephone' => '+243811111111',
                'activites' => 'Administration générale',
                'adresse_activite' => 'Siège social',
                'date_adhesion' => now(),
            ]
        );

        $this->command->info('Utilisateurs de test créés avec succès !');
    }
}