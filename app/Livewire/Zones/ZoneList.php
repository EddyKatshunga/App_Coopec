<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ZoneList extends Component
{
    use WithPagination;

    public $selectedAgenceId = null;

    public function mount()
    {
        // Agence par défaut : celle de l'utilisateur connecté ou la première disponible
        $this->selectedAgenceId = auth()->user()->agence_id ?? Agence::first()->id ?? null;
    }

    public function render()
    {
        // Détermine l'agence à filtrer
        $agenceId = auth()->user()->can('can.level6')
            ? $this->selectedAgenceId
            : auth()->user()->agence_id;

        // Récupération des zones avec leurs indicateurs actifs pré‑agrégés
        $zones = Zone::where('agence_id', $agenceId)
            ->with(['gerant'])
            ->withPerformance() // scope défini dans le modèle
            ->latest()
            ->paginate(15);

        // Calcul des totaux globaux (somme des indicateurs actifs)
        $totalCapitalCdf   = $zones->sum(fn($z) => $z->capital_actif_cdf);
        $totalCapitalUsd   = $zones->sum(fn($z) => $z->capital_actif_usd);
        $totalExpositionCdf = $zones->sum(fn($z) => $z->exposition_cdf);
        $totalExpositionUsd = $zones->sum(fn($z) => $z->exposition_usd);
        $totalCreditsActifs = $zones->sum('credits_actifs_count');
        $totalRetardsCdf    = $zones->sum(fn($z) => $z->credits_retard_actifs_cdf);
        $totalRetardsUsd    = $zones->sum(fn($z) => $z->credits_retard_actifs_usd);

        $agences = auth()->user()->can('can.level6')
            ? Agence::orderBy('nom')->get()
            : collect();

        return view('livewire.zones.zone-list', [
            'zones'   => $zones,
            'agences' => $agences,
            'statsGlobales' => [
                'capital' => [
                    'CDF' => $totalCapitalCdf,
                    'USD' => $totalCapitalUsd,
                ],
                'exposition' => [
                    'CDF' => $totalExpositionCdf,
                    'USD' => $totalExpositionUsd,
                ],
                'credits_actifs' => $totalCreditsActifs,
                'credits_retard' => $totalRetardsCdf + $totalRetardsUsd,
            ],
        ]);
    }
}