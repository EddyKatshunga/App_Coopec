<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Agence;
use Illuminate\Http\Request;

class CreditPrintController extends Controller
{
    public function index(Request $request)
    {
        $agenceId = $request->query('agence_id') ?? auth()->user()->agence_id;
        $agence = Agence::findOrFail($agenceId);

        $zones = Zone::where('agence_id', $agenceId)
            ->with(['gerant'])
            ->get();

        // Calcul des totaux (similaire à ZoneList)
        $statsGlobales = [
            'capital' => [
                'CDF' => $zones->sum(fn($z) => $z->capital_actif_cdf),
                'USD' => $zones->sum(fn($z) => $z->capital_actif_usd),
            ],
            'exposition' => [
                'CDF' => $zones->sum(fn($z) => $z->exposition_cdf),
                'USD' => $zones->sum(fn($z) => $z->exposition_usd),
            ],
            'credits_actifs' => $zones->sum(fn($z) => $z->creditsActifs()->count()),
            'credits_retard' => $zones->sum(fn($z) => $z->credits_retard_actifs_cdf + $z->credits_retard_actifs_usd),
        ];

        return view('impressions.zone-index-print', compact('zones', 'agence', 'statsGlobales'));
    }

    public function show(Zone $zone)
    {
        $zone->load(['gerant', 'agence']);
        $dashboard = $zone->getDashboardData();
        $credits_list = $zone->creditsActifs()->with('membre')->latest()->get();

        return view('impressions.zone-show-print', compact('zone', 'dashboard', 'credits_list'));
    }
}