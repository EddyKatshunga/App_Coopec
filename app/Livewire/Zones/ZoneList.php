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
        // Si l'utilisateur n'est pas SuperAdmin (Level 6), on force son agence
        if (!$user->can('can.level6')) {
            $this->selectedAgenceId = $user->agence_id ?? null;
        }else{
            $this->selectedAgenceId = auth()->user()->agence_id ?? Agence::first()->id ?? null;
        }
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->can('can.level6');
        
        // Déterminer l'ID de l'agence à afficher
        $agenceId = $isSuperAdmin ? $this->selectedAgenceId : $user->agence_id;

        $agence = null;
        $zones = collect();
        $statsGlobales = null;

        if ($agenceId) {
            $agence = Agence::with('chefAgence')->find($agenceId);
            
            if ($agence) {
                // 1. Statistiques consolidées de l'agence (USD et CDF)
                $statsGlobales = [
                    'USD' => $agence->getBilanCredits('USD'),
                    'CDF' => $agence->getBilanCredits('CDF'),
                    'termine' => $agence->credits()->termine()->count()
                ];

                // 2. Liste des zones avec Eager Loading pour éviter le problème N+1
                // On charge les zones et on pourra appeler les stats par devise dans la vue
                $zones = Zone::parAgence($agenceId)
                    ->withCount([
                        'credits as total_credits_count',
                        'credits as actifs_count' => fn($q) => $q->enCours(),
                        'credits as retards_count' => fn($q) => $q->enRetard()
                    ])
                    ->with('gerant')
                    ->orderBy('nom')
                    ->paginate(10);
            }
        }

        return view('livewire.zones.zone-list', [
            'agences' => $isSuperAdmin ? Agence::orderBy('nom')->get() : collect(),
            'agenceActuelle' => $agence,
            'statsGlobales' => $statsGlobales,
            'zones' => $zones,
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }
}