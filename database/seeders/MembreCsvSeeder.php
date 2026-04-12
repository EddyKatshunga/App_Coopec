<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\MembreService;
use App\Models\Membre;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon; // <-- Important pour les dates

class MembreCsvSeeder extends Seeder
{
    public function run(MembreService $membreService): void
    {
        $systemUser = User::first();
        if (!$systemUser) {
            $this->command->error("Aucun utilisateur trouvé.");
            return;
        }

        Auth::login($systemUser);

        $filePath = storage_path('app/membres.csv');
        if (!file_exists($filePath)) {
            $this->command->error("Fichier introuvable.");
            return;
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle, 2000, ','); 

        $count = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                
                $numeroIdentification = trim($data[3] ?? '');
                $nom = trim($data[4] ?? '');

                // On ignore les lignes vides ou l'entête
                if (empty($numeroIdentification) || $numeroIdentification === 'N° ID' || empty($nom)) {
                    continue;
                }

                if (Membre::where('numero_identification', $numeroIdentification)->exists()) {
                    $skipped++;
                    continue;
                }

                // Gestion de l'email
                $email = trim($data[11] ?? '');
                if (empty($email)) {
                    $email = $numeroIdentification . '@coopec-kwilu.com';
                }

                // Conversion des dates (du format 15/10/2002 vers 2002-10-15)
                $dateNaissance = $this->formatDate(trim($data[8] ?? ''), '1970-01-01');
                $dateAdhesion = $this->formatDate(trim($data[2] ?? ''), now()->format('Y-m-d'));

                $membreData = [
                    'nom_complet'           => $nom,
                    'email'                 => $email,
                    'password'              => 'password123',
                    'numero_identification' => $numeroIdentification,
                    'qualite'               => stripos($data[5] ?? '', 'effectif') !== false ? 'Effectif' : 'Auxiliaire',
                    'sexe'                  => (mb_substr(strtolower(trim($data[6] ?? '')), 0, 1) === 'h') ? 'M' : 'F',
                    'lieu_de_naissance'     => trim($data[7] ?? 'Inconnu'),
                    'date_de_naissance'     => $dateNaissance,
                    'adresse'               => trim($data[9] ?? 'Kikwit'),
                    'telephone'             => trim($data[10] ?? ''),
                    'activites'             => trim($data[12] ?? 'Vente divers'),
                    'adresse_activite'      => 'Kikwit',
                    'date_adhesion'         => $dateAdhesion,
                    'solde_cdf'             => (float) str_replace(',', '.', trim($data[13] ?? 0)),
                    'solde_usd'             => (float) str_replace(',', '.', trim($data[14] ?? 0)),
                ];

                $membreService->createMembre($membreData);
                $count++;
            }

            DB::commit();
            $this->command->info("Succès ! {$count} membres importés.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erreur à la ligne " . ($count + $skipped + 2) . " : " . $e->getMessage());
        } finally {
            fclose($handle);
            Auth::logout();
        }
    }

    /**
     * Transforme une date JJ/MM/AAAA en AAAA-MM-JJ
     */
    private function formatDate($dateString, $default)
    {
        if (empty($dateString)) return $default;

        try {
            // On essaie de lire le format jour/mois/année
            return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return $default;
        }
    }
}