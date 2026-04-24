<?php

namespace App\Livewire\Credits;

use App\Models\Credit;
use App\Models\Zone;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class CreditsList extends Component
{
    use WithPagination;

    public $search = '';
    public $zone_id, $statut, $devise, $actif, $date_debut, $date_fin;
    public $selected_agence_id, $all_agents = false;

    public function mount()
    {
        if (!Auth::user()->can('can.level6')) {
            $this->selected_agence_id = Auth::user()->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    /**
     * Construit la requête de base partagée entre la liste et les stats
     */
    private function getFilteredQuery()
    {
        $user = Auth::user();
        
        return Credit::query()
            ->when(!$user->can('can.level6'), fn($q) => $q->where('agence_id', $user->agence_id))
            ->when($user->can('can.level6') && $this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id))
            ->when(!$user->can('can.level4') || !$this->all_agents, function($q) use ($user) {
                if (!$user->can('can.level6')) $q->where('created_by', $user->id);
            })
            ->when($this->search, function($q) {
                $q->where(fn($sub) => $sub->where('numero_credit', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$this->search}%")));
            })
            ->when($this->zone_id, fn($q) => $q->where('zone_id', $this->zone_id))
            ->when($this->devise, fn($q) => $q->where('monnaie', $this->devise))
            ->when($this->date_debut, fn($q) => $q->whereDate('date_credit', '>=', $this->date_debut))
            ->when($this->date_fin, fn($q) => $q->whereDate('date_credit', '<=', $this->date_fin))
            ->when(!is_null($this->actif), fn($q) => $q->actif($this->actif == '1'))
            ->when($this->statut, fn($q) => $q->statut($this->statut));
    }

    public function render()
    {
        // 1. Récupération des données paginées
        $credits = $this->getFilteredQuery()
            ->with(['user', 'zone', 'agence', 'creator'])
            ->latest('date_credit')
            ->paginate(12);

        // 2. Requête agrégée (Stats globales sans charger les modèles)
        // Note: Le calcul des pénalités exactes en pur SQL est complexe, 
        // on se concentre sur le capital restant dû en base.
        $stats = $this->getFilteredQuery()
            ->selectRaw('
                COUNT(*) as count,
                SUM(capital + interet) as total_initial,
                SUM((SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0) 
                     FROM credit_remboursements WHERE credit_id = credits.id)) as total_deja_paye
            ')
            ->first();

        return view('livewire.credits.credits-list', [
            'credits' => $credits,
            'zones' => Zone::orderBy('nom')->get(),
            'agences' => Auth::user()->can('can.level6') ? Agence::all() : [],
            'stats' => [
                'total' => $stats->count ?? 0,
                'total_capital_restant' => ($stats->total_initial ?? 0) - ($stats->total_deja_paye ?? 0),
            ]
        ]);
    }
}