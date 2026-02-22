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

    private function verifierDateCloture(string $date, int $agenceId): void
    {
        $derniereTransaction = Transaction::where('agence_id', $agenceId)
            ->orderByDesc('date_transaction')
            ->first();

        if ($derniereTransaction && $date < $derniereTransaction->date_transaction) {
            throw ValidationException::withMessages([
                'date_transaction' =>
                    "Impossible d’enregistrer une opération antérieure au "
                    . \Carbon\Carbon::parse($derniereTransaction->date_transaction)->format('d/m/Y')
            ]);
        }
    }

    public function effectuerOperation(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {

            /**
             * =====================================================
             * 1. LECTURE — récupération du compte
             * =====================================================
             */
            $compte = Compte::lockForUpdate()->findOrFail($data['compte_id']);
            $agence = Agence::lockForUpdate()->findOrFail($data['agence_id']);

            $monnaie = $data['monnaie']; // CDF | USD
            $montant = $data['montant'];

            if ($montant <= 0) {
                throw ValidationException::withMessages([
                    'montant' => 'Le montant doit être strictement positif.',
                ]);
            }

            $derniereTransaction = Transaction::where('agence_id', $data['agence_id'])
                ->orderByDesc('date_transaction')
                ->first();

            if ($derniereTransaction && $data['date_transaction'] < $derniereTransaction->date_transaction) {
                throw ValidationException::withMessages([
                    'date_transaction' => 'Une transaction plus récente existe déjà pour cette agence (' . $derniereTransaction->date_transaction->format('d/m/Y') . ').',
                ]);
            }


            // Solde actuel selon la monnaie
            $soldeAvant = match ($monnaie) {
                'CDF' => $compte->solde_cdf,
                'USD' => $compte->solde_usd,
                default => throw ValidationException::withMessages([
                    'monnaie' => 'Monnaie invalide.',
                ]),
            };

            /**
             * =====================================================
             * 2. CALCUL — solde après opération
             * =====================================================
             */
            $typeOp = $data['type_transaction']; // DEPOT | RETRAIT

            if ($typeOp === 'RETRAIT' && $soldeAvant < $montant) {
                throw ValidationException::withMessages([
                    'montant' => 'Solde insuffisant pour effectuer ce retrait.',
                ]);
            }

            $soldeApres = match ($typeOp) {
                'DEPOT'   => $soldeAvant + $montant,
                'RETRAIT' => $soldeAvant - $montant,
                default  => throw ValidationException::withMessages([
                    'type_op' => 'Type d’opération invalide.',
                ]),
            };

            /**
             * =====================================================
             * 3. ÉCRITURE 1 — insertion transaction
             * =====================================================
             */
            $transaction = Transaction::create([
                'compte_id'              => $compte->id,
                'agence_id'              => $data['agence_id'],
                'agent_collecteur_id'    => $data['agent_collecteur_id'] ?? null,

                'date_transaction'       => $data['date_transaction'],
                'type_transaction'       => $typeOp,
                'montant'                => $montant,
                'monnaie'                => $monnaie,

                'solde_avant'            => $soldeAvant,
                'solde_apres'            => $soldeApres,

                'statut'                 => 'VALIDE',
            ]);

            /**
             * =====================================================
             * 4. ÉCRITURE 2 — mise à jour du compte Epargne du Membre
             * =====================================================
             */
            if ($monnaie === 'CDF') {
                $compte->update([
                    'solde_cdf' => $soldeApres,
                ]);
            } else {
                $compte->update([
                    'solde_usd' => $soldeApres,
                ]);
            }

            /**
             * =====================================================
             * 4. ÉCRITURE 3 — mise à jour du solde Epargne de l'Agence
             * =====================================================
             */
            if ($monnaie === 'CDF') {
                $agence->update([
                    'solde_epargne_cdf' => $soldeApres,
                ]);
            } else {
                $agence->update([
                    'solde_epargne_usd' => $soldeApres,
                ]);
            }

            return $transaction;
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
