<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    /**
     * Créer une transaction financière (DEPOT ou RETRAIT)
     */

    /**
     * Créer une transaction financière (DEPOT ou RETRAIT)
     */
    public function enregistrerTransactionEpargne(
        int $compte_id, 
        string $type_transaction,
        string $monnaie,
        float $montant,
        ?int $agent_collecteur_id // Rendu nullable pour plus de souplesse
    ): Transaction {
        // Utilisation de DB::transaction pour garantir l'intégrité des données
        return DB::transaction(function () use (
            $compte_id, 
            $type_transaction, 
            $monnaie, 
            $montant, 
            $agent_collecteur_id
        ) {
            // 1. Verrouillage du compte pour éviter les conditions de concurrence (Race conditions)
            $compte = Compte::lockForUpdate()->findOrFail($compte_id);

            // 2. Vérification de sécurité sur le montant
            if ($montant <= 0) {
                throw ValidationException::withMessages([
                    'montant' => 'Le montant doit être strictement positif.',
                ]);
            }

            // On utilise le solde actuel du compte verrouillé pour plus de sécurité
            $solde_actuel_reel = ($monnaie === 'CDF') ? $compte->solde_cdf : $compte->solde_usd;
            
            if ($type_transaction === 'RETRAIT' && $solde_actuel_reel < $montant) {
                throw ValidationException::withMessages([
                    'montant' => 'Opération annulée : Solde insuffisant sur le compte.',
                ]);
            }

            $solde_apres = ($type_transaction === 'DEPOT') 
                ? $solde_actuel_reel + $montant 
                : $solde_actuel_reel - $montant;

            // Insertion de la transaction
            return Transaction::create([
                'compte_id'           => $compte->id,
                'agence_id'           => auth()->user()->agence_id,
                'agent_collecteur_id' => $agent_collecteur_id,
                'user_id'             => auth()->id(), // L'utilisateur qui saisit l'opération
                'date_transaction'    => now(),
                'type_transaction'    => $type_transaction,
                'montant'             => $montant,
                'monnaie'             => $monnaie,
                'solde_avant'         => $solde_actuel_reel,
                'solde_apres'         => $solde_apres,
                'statut'              => 'VALIDE',
            ]);
        });
    }

    /**
     * Contre-passation (annulation comptable)
     */
    public function contrePasser(int $transactionId): Transaction
    {
        return DB::transaction(function () use ($transactionId) {

            $originale = Transaction::lockForUpdate()->findOrFail($transactionId);

            if ($originale->statut !== 'VALIDE') {
                throw ValidationException::withMessages([
                    'transaction' => 'Cette transaction ne peut plus être annulée.',
                ]);
            }

            $compte = Compte::lockForUpdate()->findOrFail($originale->compte_id);

            // Solde actuel
            $soldeAvant = $originale->monnaie === 'CDF'
                ? $compte->solde_cdf
                : $compte->solde_usd;

            // Inversion du mouvement
            $soldeApres = $originale->type_transaction === 'DEPOT'
                ? $soldeAvant - $originale->montant
                : $soldeAvant + $originale->montant;

            // Transaction de contre-passation
            $reversal = Transaction::create([
                'compte_id'              => $originale->compte_id,
                'agence_id'              => $originale->agence_id,
                'agent_collecteur_id'    => $originale->agent_collecteur_id,

                'date_transaction'       => now(),
                'type_transaction'       => 'CONTRE_PASSATION',
                'montant'                => $originale->montant,
                'monnaie'                => $originale->monnaie,

                'solde_avant'            => $soldeAvant,
                'solde_apres'            => $soldeApres,

                'statut'                 => 'REVERSAL',
                'reference_annulation_id'=> $originale->id,
            ]);

            // Mise à jour compte
            if ($originale->monnaie === 'CDF') {
                $compte->update(['solde_cdf' => $soldeApres]);
            } else {
                $compte->update(['solde_usd' => $soldeApres]);
            }

            // Marquer l’originale comme annulée
            $originale->update([
                'statut' => 'ANNULE',
            ]);

            return $reversal;
        });
    }
}
