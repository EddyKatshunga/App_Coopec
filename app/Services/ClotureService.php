<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\CloturesComptable;
use App\Services\ResultatTransferService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Carbon;

class ClotureService
{
    protected AccountDailyBalanceService $balanceService;
    protected ClotureVerificationService $verificationService;
    protected ResultatTransferService $resultatTransferService;

    public function __construct(
        AccountDailyBalanceService $balanceService,
        ClotureVerificationService $verificationService,
        ResultatTransferService $resultatTransferService
    ) {
        $this->balanceService = $balanceService;
        $this->verificationService = $verificationService;
        $this->resultatTransferService = $resultatTransferService;
    }

    /**
     * Ouvre une nouvelle journée comptable pour une agence.
     */
    public function ouvrirJournee(Agence $agence): CloturesComptable
    {
        return DB::transaction(function () use ($agence) {
            // Vérification rapide (déjà faite en UI, mais on sécurise)
            $ouverte = CloturesComptable::where('agence_id', $agence->id)
                ->where('statut', 'ouverte')
                ->exists();
            if ($ouverte) {
                throw new Exception("Une journée est déjà ouverte pour cette agence.");
            }

            $user = Auth::user();
            if (!$user) {
                throw new Exception("Utilisateur non authentifié.");
            }

            $cloture = CloturesComptable::create([
                'agence_id'    => $agence->id,
                'date_cloture' => Carbon::create(2026, 5, 6),
                //'date_cloture' => Carbon::today(),
                'statut'       => 'ouverte',
                'created_by'   => $user->id,
                'updated_by'   => $user->id,
            ]);

            return $cloture;
        });
    }

    /**
     * Clôture une journée ouverte.
     *
     * @param CloturesComptable $cloture
     * @param array $donneesPhysiques Contient 'observation' (optionnel)
     * @return bool
     * @throws Exception
     */
    public function cloturerJournee(CloturesComptable $cloture, array $donneesPhysiques): bool
    {
        if ($cloture->statut === 'cloturee') {
            throw new Exception("Seule une journée ouverte ou verouillee peut être clôturée. Statut actuel : {$cloture->statut}");
        }
        
        // Vérification critique (même si l'UI l'a déjà faite, on re-vérifie)
        if ($this->verificationService->hasEcrituresPosterieures($cloture)) {
            throw new Exception("Impossible de clôturer : des écritures existent avec une date postérieure à la date de clôture.");
        }
        
        return DB::transaction(function () use ($cloture, $donneesPhysiques) {
            // 1. Transférer les charges/produits vers le résultat net (cette écriture aura la même date de clôture)
            $this->resultatTransferService->transfererResultatJournee($cloture);
            
            // 2. Geler les soldes de tous les comptes (y compris le compte résultat net)
            $this->balanceService->computeDailyBalances($cloture);

            // 3. Marquer la journée comme clôturée
            $cloture->statut = 'cloturee';
            $cloture->observation_cloture = $donneesPhysiques['observation'] ?? null;
            $cloture->updated_by = Auth::id();
            $cloture->save();

            return true;
        });
    }
}