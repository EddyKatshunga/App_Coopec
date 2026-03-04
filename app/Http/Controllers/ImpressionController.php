<?php

namespace App\Http\Controllers;

use App\Models\CloturesComptable;
use App\Models\Compte;
use Illuminate\Http\Request;

class ImpressionController extends Controller
{
    public function releveIndividuel(Request $request, Compte $compte, $debut = null, $fin = null, $monnaie = null)
    {
        // 2. Construction de la requête filtrée
        $query = $compte->transactions()
            ->whereBetween('date_transaction', [$debut, $fin])
            ->with(['agent_collecteur.user', 'creator'])
            ->oldest('date_transaction');

        if ($monnaie) $query->where('monnaie', $monnaie);

        $items = $query->get();

        // 3. Retour vers une vue spécifique au relevé de compte (différente de la clôture)
        return view('impressions.releve_individuel_compte', [
            'compte' => $compte,
            'items' => $items,
            'titre' => "RELEVÉ DE COMPTE",
            'filtres' => [
                'debut' => $debut,
                'fin' => $fin,
                'monnaie' => $monnaie ?: 'Toutes',
            ]
        ]);
    }

    public function releve(CloturesComptable $cloture, $type)
    {
        $data = ['cloture' => $cloture];
        
        switch ($type) {
            case 'epargne':
                $data['titre'] = "RELEVÉ JOURNALIER DES ÉPARGNES";
                $data['items'] = $cloture->transactionsEpargne()->with(['compte.user', 'agent_collecteur.user'])->oldest()->get();
                return view('impressions.releve_epargne', $data);
                
            case 'remboursements':
                $data['titre'] = "RELEVÉ JOURNALIER DES REMBOURSEMENTS";
                $data['items'] = $cloture->remboursements()->with(['credit.user', 'zone'])->oldest()->get();
                return view('impressions.releve_remboursements', $data);
                
            case 'credits':
                $data['titre'] = "RELEVÉ JOURNALIER DES CRÉDITS OCTROYÉS";
                $data['items'] = $cloture->credits()->with(['user', 'zone'])->oldest()->get();
                return view('impressions.releve_credits', $data);
            
            default:
                abort(404);
        }
    }
}