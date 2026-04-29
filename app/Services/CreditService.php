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
        $nb_credit = $membre->credits()->count() + 1;
        
        return Credit::create([
            'numero_credit' => $nb_credit . "-". $membre->numero_identification, 
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
            'observation'      => $data['observation'] ?? null,
        ]);
    }

    /**
     * Cloturer un dossier.
     * * @param Credit $credit
     * @param string|null $motif
     * @param bool $estNegocie
     * @return Credit
     */
    public function forcerCloture(Credit $credit, ?string $motif = null, bool $estNegocie = true): Credit
    {
        $credit->fill([
            'date_cloture_forcee' => now(),
            'negocie'             => $estNegocie,
            'note_negociation'    => $motif ?? 'Clôture forcée par l\'administration.',
            'observation'         => $credit->observation . " [Clôturé manuellement par " . Auth::user()->name . "]",
            'statut'              => "termine"
        ]);

        // Sauvegarde sans déclencher d'événements
        $credit->updateQuietly();

        return $credit;
    }
}