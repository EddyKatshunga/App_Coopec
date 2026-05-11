<?php
// app/Services/ResultatTransferService.php

namespace App\Services;

use App\Models\Account;
use App\Models\CloturesComptable;
use App\Models\JournalEntryLine;
use App\Helpers\AccountingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultatTransferService
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Transfère le solde des comptes de charge/produit vers le compte "Résultat net"
     * et annule ces comptes pour la journée de clôture.
     */
    public function transfererResultatJournee(CloturesComptable $cloture): void
    {
        try {
            $agenceId = $cloture->agence_id;
            $dateCloture = $cloture->date_cloture;

            Log::info("Début du transfert de résultat pour l'agence ID: {$agenceId} à la date du " . $dateCloture->format('Y-m-d'));

            // Récupérer le compte "Résultat net" (numéro 12)
            $compteResultatNet = Account::where('numero', '12')->first();
            if (!$compteResultatNet) {
                Log::error("Erreur Clôture : Le compte 'Résultat net' (n°12) est introuvable en base de données.");
                throw new \Exception("Le compte 'Résultat net' (n°12) n'existe pas. Veuillez l'ajouter au plan comptable.");
            }
            
            $lignesTransfert = [];

            // 1. Recupère la somme des opérations pour chaque Compte de charge
            $charges = JournalEntryLine::whereHas('journalEntry', function ($q) use ($agenceId, $cloture) {
                    $q->where('agence_id', $agenceId)
                      ->where('journee_comptable_id', $cloture->id);
                })
                ->whereHas('account', function ($q) {
                    $q->where('type', 'charge');
                })
                ->selectRaw('account_id, SUM(montant_base) as solde_cdf')
                ->groupBy('account_id')
                ->get();
            
            foreach ($charges as $charge) {
                $solde = (float) $charge->solde_cdf;
                if (abs($solde) < 0.01) continue;

                $lignesTransfert[] = AccountingHelper::credit($charge->account_id, $solde, 'CDF');
                $lignesTransfert[] = AccountingHelper::debit($compteResultatNet->id, $solde, 'CDF');
            }
            
            // 2. Comptes de produit
            $produits = JournalEntryLine::whereHas('journalEntry', function ($q) use ($agenceId, $cloture) {
                    $q->where('agence_id', $agenceId)
                      ->where('journee_comptable_id', $cloture->id);
                })
                ->whereHas('account', function ($q) {
                    $q->where('type', 'produit');
                })
                ->selectRaw('account_id, SUM(montant_base) as solde_cdf')
                ->groupBy('account_id')
                ->get();

            foreach ($produits as $produit) {
                $solde = (float) $produit->solde_cdf;
                if (abs($solde) < 0.01) continue;

                $lignesTransfert[] = AccountingHelper::debit($produit->account_id, $solde, 'CDF');
                $lignesTransfert[] = AccountingHelper::credit($compteResultatNet->id, $solde, 'CDF');
            }

            if (empty($lignesTransfert)) {
                Log::warning("Transfert Résultat : Aucun mouvement de charge ou produit détecté pour cette journée.");
                return;
            }
            
            // Créer une seule écriture avec toutes les lignes
            $libelle = "Transfert des résultats de la journée du " . $dateCloture->format('d/m/Y');
            
            $this->accountingService->record($lignesTransfert, $libelle, null, $cloture);
            
            Log::info("Succès : Transfert de résultat effectué avec succès (" . count($lignesTransfert) . " lignes générées).");
            
        } catch (\Exception $e) {
            // Log critique de l'erreur avec la stack trace pour le debug
            Log::error("ÉCHEC du transfert de résultat : " . $e->getMessage(), [
                'agence_id' => $cloture->agence_id,
                'cloture_id' => $cloture->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // On re-balance l'exception pour que le processus parent (le contrôleur ou le job) sache que ça a échoué
            throw $e;
        }
    }
}
