<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Agence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditPrintController extends Controller
{
     /**
     * Affiche la vue imprimable de la situation générale des crédits pour une agence.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agenceId = $request->query('agence_id');

        // Vérifier les droits d'accès à l'agence
        if (!$user->can('can.level6') && $user->agence_id != $agenceId) {
            abort(403, 'Accès non autorisé à cette agence.');
        }

        $agence = Agence::with('chefAgence')->findOrFail($agenceId);

        // Statistiques consolidées de l'agence (USD et CDF)
        $statsGlobales = [
            'USD' => $agence->getBilanCredits('USD'),
            'CDF' => $agence->getBilanCredits('CDF'),
            'termine' => $agence->credits()->termine()->count()
        ];

        // Récupération de toutes les zones de l'agence (non paginé pour l'impression)
        $zones = $agence->zones()
            ->with(['gerant'])
            ->withCount([
                'credits as total_credits_count',
                'credits as actifs_count' => fn($q) => $q->enCours(),
                'credits as retards_count' => fn($q) => $q->enRetard()
            ])
            ->orderBy('nom')
            ->get();

        // Pour chaque zone, on pré-calcule les statistiques USD/CDF
        // (on peut le faire dans la vue avec les accesseurs, mais on le fait ici pour éviter les requêtes N+1)
        $zones->each(function ($zone) {
            $zone->stats_usd = $zone->getStatsPortefeuille('USD');
            $zone->stats_cdf = $zone->getStatsPortefeuille('CDF');
        });

        return view('impressions.zone-index-print', compact('agence', 'statsGlobales', 'zones'));
    }

    public function show(Zone $zone)
    {
        $zone->load(['gerant', 'agence']);
        $dashboard = $zone->getDashboardData();
        $credits_list = $zone->creditsActifs()->with('membre')->latest()->get();

        return view('impressions.zone-show-print', compact('zone', 'dashboard', 'credits_list'));
    }
}