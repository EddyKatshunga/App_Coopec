<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\CloturesComptable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Carbon;

class ClotureService
{
    protected AccountDailyBalanceService $balanceService;
    protected ClotureVerificationService $verificationService;

    public function __construct(
        AccountDailyBalanceService $balanceService,
        ClotureVerificationService $verificationService
    ) {
        $this->balanceService = $balanceService;
        $this->verificationService = $verificationService;
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
                'date_cloture' => Carbon::today(),
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
        if ($cloture->statut !== 'ouverte') {
            throw new Exception("Seule une journée ouverte peut être clôturée. Statut actuel : {$cloture->statut}");
        }

        // Vérification critique (même si l'UI l'a déjà faite, on re-vérifie)
        if ($this->verificationService->hasEcrituresPosterieures($cloture)) {
            throw new Exception("Impossible de clôturer : des écritures existent avec une date postérieure à la date de clôture.");
        }

        return DB::transaction(function () use ($cloture, $donneesPhysiques) {
            // 1. Geler les soldes de tous les comptes
            $this->balanceService->computeDailyBalances($cloture);

            // 2. Marquer la journée comme clôturée
            $cloture->statut = 'cloturee';
            $cloture->observation_cloture = $donneesPhysiques['observation'] ?? null;
            $cloture->updated_by = Auth::id();
            $cloture->save();

            return true;
        });
    }
}