<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CloturesComptable;
use App\Models\Compte;
use App\Models\Membre;
use App\Models\Agence;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
                $compteEpargne = Account::where('numero', '41')->first();
                $data['compte_epargne'] = $compteEpargne;
                $data['titre'] = "RELEVÉ JOURNALIER DES ÉPARGNES";
                $data['items'] = $cloture->transactions()->with(['compte.user', 'agent_collecteur.user'])->oldest()->get();
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

    public function releveCompte(Account $account, Request $request)
    {
        // Récupération des filtres (identiques à ceux du Livewire)
        $agenceId = $request->get('agence_id', Auth::user()->agence_id ?? Agence::first()->id);
        $dateDebut = $request->get('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin', now()->format('Y-m-d'));

        $devises = ['USD', 'CDF'];
        $stats = [];

        // Requête de base pour les lignes d'écriture de la période
        $baseQuery = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($agenceId, $dateDebut, $dateFin) {
                if ($agenceId) {
                    $q->where('agence_id', $agenceId);
                }
                $q->whereBetween('date_operation', [
                    Carbon::parse($dateDebut)->startOfDay(),
                    Carbon::parse($dateFin)->endOfDay()
                ]);
            });

        // Totaux par devise sur la période
        $totalsPeriod = $baseQuery->selectRaw('monnaie, SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('monnaie')
            ->get()
            ->keyBy('monnaie');

        // Clôture précédant la période (pour le report)
        $cloture = null;
        if ($agenceId) {
            $dateAvant = Carbon::parse($dateDebut)->subDay()->toDateString();
            $cloture = CloturesComptable::getPreviousCloture($dateAvant, $agenceId);
        }

        foreach ($devises as $devise) {
            $periodDebit  = (float) ($totalsPeriod->get($devise)?->total_debit ?? 0);
            $periodCredit = (float) ($totalsPeriod->get($devise)?->total_credit ?? 0);

            // Solde initial = solde_fin de la dernière clôture avant la période
            $soldeInitial = 0;
            if ($cloture) {
                $balance = $cloture->getAccountDailyBalance($account, $devise);
                if ($balance) {
                    $soldeInitial = (float) $balance->solde_fin;
                }
            }

            // Variation selon le type de compte (règle identique)
            if ($account->type === 'charge' || $account->type === 'produit') {
                $variationPeriode = $periodCredit - $periodDebit;
            } else {
                $variationPeriode = $periodDebit - $periodCredit;
            }

            $soldeFinal = $soldeInitial + $variationPeriode;

            $stats[$devise] = compact('periodDebit', 'periodCredit', 'soldeInitial', 'soldeFinal');
        }

        // Liste des mouvements (identique au Livewire)
        $mouvements = JournalEntryLine::with(['journalEntry.agence'])
            ->where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($agenceId, $dateDebut, $dateFin) {
                if ($agenceId) $q->where('agence_id', $agenceId);
                $q->whereBetween('date_operation', [
                    Carbon::parse($dateDebut)->startOfDay(),
                    Carbon::parse($dateFin)->endOfDay()
                ]);
            })
            ->orderBy('id', 'desc')
            ->get();

        $agence = $agenceId ? Agence::find($agenceId) : null;

        return view('impressions.account_statement', compact('account', 'stats', 'mouvements', 'dateDebut', 'dateFin', 'agence'));
    }

}