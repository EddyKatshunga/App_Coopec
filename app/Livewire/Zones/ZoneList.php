<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use App\Models\Credit;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class ZoneList extends Component
{
    use WithPagination, AuthorizesRequests;

    public $selectedAgenceId = null;

    public function mount()
    {
        $user = auth()->user();
        
        // Si l'utilisateur n'est pas super admin, on force l'agence
        if (!$user->can('can.level6')) {
            $this->selectedAgenceId = $user->agence_id;
        }
        // Sinon (super admin) on laisse le filtre à null (toutes agences)
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->can('can.level6');
        $agenceId = $isSuperAdmin ? $this->selectedAgenceId : $user->agence_id;

        // 1. Requête des Zones avec le nouveau scope optimisé
        $zoneQuery = Zone::query();
        if ($agenceId) {
            $zoneQuery->where('agence_id', $agenceId);
        }

        $zones = $zoneQuery
            ->withDetailedStats()   // ← scope qui charge tous les indicateurs
            ->latest()
            ->paginate(15);

        // 2. Statistiques globales en une seule requête (regroupées par agence si filtre)
        $globalStats = Credit::query()
            ->when($agenceId, fn($q) => $q->where('agence_id', $agenceId))
            ->actif()
            ->selectRaw('
                SUM(CASE WHEN monnaie = "CDF" THEN capital ELSE 0 END) as cap_cdf,
                SUM(CASE WHEN monnaie = "USD" THEN capital ELSE 0 END) as cap_usd,
                SUM(CASE WHEN monnaie = "CDF" THEN (capital + interet) ELSE 0 END) as expo_cdf,
                SUM(CASE WHEN monnaie = "USD" THEN (capital + interet) ELSE 0 END) as expo_usd,
                COUNT(*) as total_actifs,
                SUM(CASE WHEN statut = "en_retard" AND monnaie = "CDF" THEN 1 ELSE 0 END) as retard_cdf,
                SUM(CASE WHEN statut = "en_retard" AND monnaie = "USD" THEN 1 ELSE 0 END) as retard_usd
            ')
            ->first();

        $agences = $isSuperAdmin ? Agence::orderBy('nom')->get() : collect();

        return view('livewire.zones.zone-list', [
            'zones'   => $zones,
            'agences' => $agences,
            'statsGlobales' => [
                'capital' => [
                    'CDF' => (float) ($globalStats->cap_cdf ?? 0),
                    'USD' => (float) ($globalStats->cap_usd ?? 0),
                ],
                'exposition' => [
                    'CDF' => (float) ($globalStats->expo_cdf ?? 0),
                    'USD' => (float) ($globalStats->expo_usd ?? 0),
                ],
                'credits_actifs' => (int) ($globalStats->total_actifs ?? 0),
                'credits_retard' => (int) (($globalStats->retard_cdf ?? 0) + ($globalStats->retard_usd ?? 0)),
            ],
        ]);
    }
}