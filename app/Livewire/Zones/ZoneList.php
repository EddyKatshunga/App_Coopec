<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use App\Models\Credit;
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
        $user = auth()->user();
        // Pour un non‑super admin, on force l’agence de l’utilisateur
        if (!$user->can('can.level6')) {
            $this->selectedAgenceId = $user->agence_id;
        } else {
            // Pour le super admin, on prend la première agence par défaut si aucune n’est sélectionnée
            $this->selectedAgenceId = $this->selectedAgenceId ?? Agence::first()->id ?? null;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->can('can.level6');
        $agenceId = $isSuperAdmin ? $this->selectedAgenceId : $user->agence_id;

        // On prépare la date actuelle en PHP pour éviter les fonctions SQL natives divergentes
        $now = now()->toDateTimeString();

        $zoneQuery = Zone::query();
        if ($agenceId) {
            $zoneQuery->where('agence_id', $agenceId);
        }

        $zones = $zoneQuery->clone()
            ->with(['gerant'])
            ->withPerformance() // ATTENTION : Vérifiez aussi ce scope dans le modèle Zone !
            ->latest()
            ->paginate(15);

        $creditStats = Credit::query()
            ->whereHas('zone', function ($q) use ($agenceId) {
                if ($agenceId) {
                    $q->where('agence_id', $agenceId);
                }
            })
            ->actif()
            ->selectRaw('
                COALESCE(SUM(CASE WHEN monnaie = "CDF" THEN capital ELSE 0 END), 0) as capital_cdf,
                COALESCE(SUM(CASE WHEN monnaie = "USD" THEN capital ELSE 0 END), 0) as capital_usd,
                COALESCE(SUM(CASE WHEN monnaie = "CDF" THEN capital + interet ELSE 0 END), 0) as exposition_cdf,
                COALESCE(SUM(CASE WHEN monnaie = "USD" THEN capital + interet ELSE 0 END), 0) as exposition_usd,
                COUNT(*) as total_credits_actifs,
                COALESCE(SUM(CASE WHEN monnaie = "CDF" AND date_fin_prevue < ? THEN 1 ELSE 0 END), 0) as retards_cdf,
                COALESCE(SUM(CASE WHEN monnaie = "USD" AND date_fin_prevue < ? THEN 1 ELSE 0 END), 0) as retards_usd
            ', [$now, $now]) // On passe $now ici en paramètre de liaison (bindings)
            ->first();

        // Liste des agences pour le filtre (uniquement pour les super admins)
        $agences = $isSuperAdmin ? Agence::orderBy('nom')->get() : collect();

        return view('livewire.zones.zone-list', [
            'zones'   => $zones,
            'agences' => $agences,
            'statsGlobales' => [
                'capital' => [
                    'CDF' => (float) $creditStats->capital_cdf,
                    'USD' => (float) $creditStats->capital_usd,
                ],
                'exposition' => [
                    'CDF' => (float) $creditStats->exposition_cdf,
                    'USD' => (float) $creditStats->exposition_usd,
                ],
                'credits_actifs' => (int) $creditStats->total_credits_actifs,
                'credits_retard' => (int) ($creditStats->retards_cdf + $creditStats->retards_usd),
            ],
        ]);
    }
}