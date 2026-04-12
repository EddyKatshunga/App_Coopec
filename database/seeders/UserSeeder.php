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
    protected function createUserWithRole(array $userData, string $roleName): void
    {
        DB::transaction(function () use ($userData, $roleName) {

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            $user->assignRole($roleName);
        });
    }

    public function run(): void
    {
        $this->createUserWithRole(
            userData: [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => 'password123',
            ],
            roleName: 'niveau 8',
        );

        $this->command->info('Admin créé avec succès !');
    }
}