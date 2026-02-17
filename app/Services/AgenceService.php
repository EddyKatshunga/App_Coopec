<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\Agent;
use App\Models\AgenceDirectionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class AgenceService
{
    /*
    |--------------------------------------------------------------------------
    | CRÉATION D’UNE AGENCE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Agence
    {
        return DB::transaction(function () use ($data) {

            if (($data['solde_actuel_coffre'] ?? 0) < 0) {
                throw ValidationException::withMessages([
                    'solde_actuel_coffre' => 'Le solde initial du coffre ne peut pas être négatif.'
                ]);
            }

            return Agence::create([
                'nom' => $data['nom'],
                'code' => $data['code'],
                'ville' => $data['ville'],
                'pays' => $data['pays'],
                'directeur_id' => null,
                'solde_actuel_coffre' => $data['solde_actuel_coffre'] ?? 0,
                'solde_actuel_epargne' => 0,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION BASIQUE D’UNE AGENCE
    |--------------------------------------------------------------------------
    */

    public function update(Agence $agence, array $data): Agence
    {
        return DB::transaction(function () use ($agence, $data) {

            $agence->update([
                'nom' => $data['nom'],
                'code' => $data['code'],
                'ville' => $data['ville'],
                'pays' => $data['pays'],
            ]);

            return $agence;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGER LE DIRECTEUR (OPÉRATION SENSIBLE)
    |--------------------------------------------------------------------------
    */

    public function changerDirecteur(Agence $agence, Agent $nouveauDirecteur): void
    {
        DB::transaction(function () use ($agence, $nouveauDirecteur) {

            // Vérifier que l'agent appartient bien à l'agence
            if ($nouveauDirecteur->agence_id !== $agence->id) {
                throw ValidationException::withMessages([
                    'directeur' => 'Cet agent n’appartient pas à cette agence.'
                ]);
            }

            // Vérifier qu'il possède un user lié
            if (!$nouveauDirecteur->user) {
                throw new Exception("L’agent sélectionné ne possède pas de compte utilisateur.");
            }

            // Empêcher qu’il soit directeur ailleurs
            $dejaDirecteur = Agence::where('directeur_id', $nouveauDirecteur->id)
                ->where('id', '!=', $agence->id)
                ->exists();

            if ($dejaDirecteur) {
                throw ValidationException::withMessages([
                    'directeur' => 'Cet agent est déjà directeur d’une autre agence.'
                ]);
            }

            $ancienDirecteur = $agence->directeur;

            /*
            |--------------------------------------------------------------------------
            | MODIFICATION DES RÔLES
            |--------------------------------------------------------------------------
            */

            // 1️⃣ Ancien directeur → superviseur
            if ($ancienDirecteur && $ancienDirecteur->user) {
                $ancienDirecteur->user->syncRoles(['superviseur']);
            }

            // 2️⃣ Nouveau directeur → directeur
            $nouveauDirecteur->user->syncRoles(['chef_agence']);

            /*
            |--------------------------------------------------------------------------
            | MISE À JOUR DE L’AGENCE
            |--------------------------------------------------------------------------
            */

            $agence->update([
                'directeur_id' => $nouveauDirecteur->id
            ]);

            /*
            |--------------------------------------------------------------------------
            | HISTORIQUE (fortement recommandé)
            |--------------------------------------------------------------------------
            */

            AgenceDirectionHistory::create([
                'agence_id' => $agence->id,
                'ancien_directeur_id' => $ancienDirecteur?->id,
                'nouveau_directeur_id' => $nouveauDirecteur->id,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AJOUT DE FONDS AU COFFRE
    |--------------------------------------------------------------------------
    */

    public function ajouterFondsCoffre(Agence $agence, float $montant): void
    {
        if ($montant <= 0) {
            throw ValidationException::withMessages([
                'montant' => 'Le montant doit être supérieur à zéro.'
            ]);
        }

        DB::transaction(function () use ($agence, $montant) {

            $agence->increment('solde_actuel_coffre', $montant);

            // Ici tu peux enregistrer un mouvement comptable si nécessaire
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RETRAIT DE FONDS DU COFFRE
    |--------------------------------------------------------------------------
    */

    public function retirerFondsCoffre(Agence $agence, float $montant): void
    {
        if ($montant <= 0) {
            throw ValidationException::withMessages([
                'montant' => 'Le montant doit être supérieur à zéro.'
            ]);
        }

        if ($agence->solde_actuel_coffre < $montant) {
            throw ValidationException::withMessages([
                'montant' => 'Solde insuffisant dans le coffre.'
            ]);
        }

        DB::transaction(function () use ($agence, $montant) {

            $agence->decrement('solde_actuel_coffre', $montant);

            // Ici tu peux enregistrer un mouvement comptable si nécessaire
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VÉRIFIER SI AGENCE FINALISÉE
    |--------------------------------------------------------------------------
    */

    public function estFinalisee(Agence $agence): bool
    {
        return $agence->directeur_id !== null
            && $agence->solde_actuel_coffre >= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION SÉCURISÉE
    |--------------------------------------------------------------------------
    */

    public function delete(Agence $agence): void
    {
        if ($agence->agents()->count() > 0) {
            throw ValidationException::withMessages([
                'agence' => 'Impossible de supprimer une agence contenant des agents.'
            ]);
        }

        DB::transaction(function () use ($agence) {
            $agence->delete();
        });
    }
}
