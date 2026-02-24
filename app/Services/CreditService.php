<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\Membre;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CreditService
{
    /**
     * Logique de création d'un crédit.
     * * @param Membre $membre
     * @param array $data
     * @return Credit
     */
    public function creerCredit(Membre $membre, array $data): Credit
    {
        return Credit::create([
            'numero_credit' => strtoupper(Str::uuid()),
            'membre_id' => $membre->id,
            'user_id'   => $membre->user->id,
            'zone_id'   => $data['zone_id'],
            'agent_id'  => $data['agent_id'],
            'agence_id' => Auth::user()->agence_id,
            'monnaie'                  => $data['monnaie'],
            'capital'                  => $data['capital'],
            'interet'                  => $data['interet'],
            'taux_penalite_journalier' => $data['taux_penalite_journalier'],
            'unite_temps'      => $data['unite_temps'],
            'duree'            => $data['duree'],
            'date_fin_prevue'  => $data['date_fin'],
            'garant_nom'       => $data['garant_nom'],
            'garant_adresse'   => $data['garant_adresse'] ?? null,
            'garant_telephone' => $data['garant_telephone'] ?? null,
            'statut'           => 'en_cours',
            'observation'      => $data['observation'] ?? 'Rien à signaler',
        ]);
    }
}