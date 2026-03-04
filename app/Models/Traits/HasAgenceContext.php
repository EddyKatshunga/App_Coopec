<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAgenceContext
{
    /**
     * Vérifie si l'utilisateur appartient à une agence.
     * Sinon, déclenche une erreur 403.
     */
    public function secureAgenceContext()
    {
        $agence = Auth::user()->agence;

        if (!$agence) {
            abort(403, "Action impossible : votre compte n'est rattaché à aucune agence active.");
        }

        return $agence;
    }
    
    //Retourne un objet CloturesComptable
    public function secureJourneeContext()
    {
        $journee_ouverte = Auth::user()->journee_ouverte;

        if (!$journee_ouverte) {
            abort(403, "Action impossible : aucune journée ouverte.");
        }

        return $journee_ouverte;
    }
}