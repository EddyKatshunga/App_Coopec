<?php

namespace App\Http\Controllers;

use App\Models\CloturesComptable;
use App\Models\Compte;
use App\Models\Membre;

class ImpressionController extends Controller
{
    //Relevé d'un compte Epargne
    public function releveIndividuel(Compte $compte, $debut = null, $fin = null, $monnaie = null)
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


    public function rapportJournalier(CloturesComptable $cloture)
    {
        // On prépare les données groupées comme dans la vue de détails
        $data = [
            'cloture' => $cloture,
            'titre' => "RAPPORT DE LA JOURNÉE COMPTABLE",
            
            'revenusGroupes' => $cloture->revenus()
                ->with('typeRevenu')
                ->get()
                ->groupBy(['type_revenu_id', 'monnaie']),

            'depensesGroupes' => $cloture->depenses()
                ->with('typeDepense')
                ->get()
                ->groupBy(['type_depense_id', 'monnaie']),

            'depotsGroupes' => $cloture->depots()
                ->with('agent_collecteur.user')
                ->get()
                ->groupBy(['agent_collecteur_id', 'monnaie']),

            'retraitsGroupes' => $cloture->retraits()
                ->with('creator')
                ->get()
                ->groupBy(['created_by', 'monnaie']),

            'remboursementsGroupes' => $cloture->remboursements()
                ->with('zone')
                ->get()
                ->groupBy(['zone_id', 'monnaie']),

            'creditsGroupes' => $cloture->credits()
                ->with('zone')
                ->get()
                ->groupBy(['zone_id', 'monnaie']),
        ];

        return view('impressions.rapport_journalier', $data);
    }

    public function ficheMembre(Membre $membre)
    {
        // Chargement des relations pour avoir une fiche complète
        $membre->load(['user', 'agentParrain.user', 'comptes', 'credits.remboursements']);
        
        return view('impressions.fiche_membre', [
            'membre' => $membre,
            'titre' => "FICHE SIGNALÉTIQUE MEMBRE"
        ]);
    }

    public function rapportPeriodique($agenceId, $debut, $fin)
    {
        $query = CloturesComptable::where('agence_id', $agenceId)
            ->whereBetween('date_cloture', [$debut, $fin])
            ->orderBy('date_cloture', 'asc');

        $clotures = $query->get();
        $agence = \App\Models\Agence::find($agenceId);

        // Calcul des totaux globaux pour le pied de page du rapport (uniquement les flux)
        $totaux = [
            'depot_usd' => $clotures->sum('total_depot_usd'),
            'depot_cdf' => $clotures->sum('total_depot_cdf'),
            'rembourse_usd' => $clotures->sum('total_remboursement_usd'),
            'rembourse_cdf' => $clotures->sum('total_remboursement_cdf'),
            'revenu_usd' => $clotures->sum('total_revenu_usd'), // Ajout
            'revenu_cdf' => $clotures->sum('total_revenu_cdf'), // Ajout
            
            'retrait_usd' => $clotures->sum('total_retrait_usd'),
            'retrait_cdf' => $clotures->sum('total_retrait_cdf'),
            'credit_usd' => $clotures->sum('total_credit_usd'),
            'credit_cdf' => $clotures->sum('total_credit_cdf'),
            'depense_usd' => $clotures->sum('total_depense_usd'),
            'depense_cdf' => $clotures->sum('total_depense_cdf'),
        ];

        return view('impressions.rapport_periodique', [
            'clotures' => $clotures,
            'agence' => $agence,
            'totaux' => $totaux,
            'titre' => "RAPPORT PÉRIODIQUE D'ACTIVITÉS",
            'filtres' => [
                'debut' => \Carbon\Carbon::parse($debut)->format('d/m/Y'),
                'fin' => \Carbon\Carbon::parse($fin)->format('d/m/Y'),
            ]
        ]);
    }

}