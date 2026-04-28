<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Agence;
use App\Models\Credit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class ZoneList extends Component
{
    use WithPagination, AuthorizesRequests;

    public $selectedAgenceId = null;

    public function mount()
    {
        $user = auth()->user();
        if (!$user->can('can.level6')) {
            $this->selectedAgenceId = $user->agence_id;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->can('can.level6');
        $agenceId = $isSuperAdmin ? $this->selectedAgenceId : $user->agence_id;

        // 1. Liste des zones paginée (avec stats par zone)
        $zones = Zone::query()
            ->when($agenceId, fn($q) => $q->where('agence_id', $agenceId))
            ->withDetailedStats()
            ->with(['gerant'])
            ->latest()
            ->paginate(15);

        // 2. Calcul des totaux globaux (pour le tfoot)
        // On interroge directement la table Credit pour la précision globale
        $totals = Credit::query()
            ->actif()
            ->when($agenceId, fn($q) => $q->where('agence_id', $agenceId))
            ->selectRaw('
                COUNT(*) as total_nb,
                SUM(CASE WHEN monnaie = "USD" THEN (capital + interet) ELSE 0 END) as vol_usd,
                SUM(CASE WHEN monnaie = "USD" THEN total_remboursement ELSE 0 END) as enc_usd,
                SUM(CASE WHEN monnaie = "CDF" THEN (capital + interet) ELSE 0 END) as vol_cdf,
                SUM(CASE WHEN monnaie = "CDF" THEN total_remboursement ELSE 0 END) as enc_cdf
            ')
            ->first();

        return view('livewire.zones.zone-list', [
            'zones' => $zones,
            'totals' => $totals,
            'agences' => $isSuperAdmin ? Agence::orderBy('nom')->get() : collect(),
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }
}