<?php

namespace App\Livewire\Credits;

use App\Models\Credit;
use App\Models\Zone;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CreditsList extends Component
{
    use WithPagination;

    public $search = '';
    public $zone_id = null;
    public $statut = null;
    public $date_debut = null;
    public $date_fin = null;
    
    // Filtres de périmètre
    public $selected_agence_id = null;
    public $all_agents = false;

    public function mount()
    {
        $user = Auth::user();
        if (!$user->can('can.level6')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        // Optimisation Eager Loading : on charge tout pour éviter les requêtes N+1 dans la grille
        $query = Credit::with(['user', 'zone', 'remboursements', 'agence', 'creator']);

        // --- SÉCURITÉ NIVEAU 3 : Admin Global ---
        if ($user->can('can.level6')) {
            $query->when($this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id));
        } 
        // --- SÉCURITÉ NIVEAU 1 & 2 : Restriction Agence ---
        else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- SÉCURITÉ NIVEAU 1 : Restriction Personnelle ---
        if (!$user->can('can.level4') || !$this->all_agents) {
            if (!$user->can('can.level6')) {
                $query->where('created_by', $user->id);
            }
        }

        // --- FILTRES MÉTIERS ---
        $query->when($this->search, function($q) {
            $q->where(function($sub) {
                $sub->where('numero_credit', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        })
        ->when($this->zone_id, fn($q) => $q->where('zone_id', $this->zone_id))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_credit', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_credit', '<=', $this->date_fin))
        // Nouveau bloc pour le statut
        ->when($this->statut, function($q) {
            $today = now()->format('Y-m-d');
            
            // Sous-requête pour calculer le total payé (Capital + Intérêt)
            $subQueryPaye = "(SELECT COALESCE(SUM(montant_capital_payee + montant_interet_payee), 0) 
                            FROM credit_remboursements 
                            WHERE credit_remboursements.credit_id = credits.id)";

            switch ($this->statut) {
                case 'termine_negocie':
                    $q->whereNotNull('date_cloture_forcee')->where('negocie', true);
                    break;

                case 'en_cours':
                    $q->whereRaw("($subQueryPaye < (capital + interet))")
                    ->whereDate('date_fin_prevue', '>=', $today)
                    ->whereNull('date_cloture_forcee');
                    break;

                case 'en_retard':
                    $q->whereRaw("($subQueryPaye < (capital + interet))")
                    ->whereDate('date_fin_prevue', '<', $today)
                    ->whereNull('date_cloture_forcee');
                    break;

                case 'termine':
                case 'termine_en_retard':
                    // Pour simplifier, on considère terminé si le solde de base est à 0
                    $q->whereRaw("($subQueryPaye >= (capital + interet))");
                    
                    if ($this->statut === 'termine_en_retard') {
                        // On vérifie si le dernier paiement a eu lieu après l'échéance
                        $q->whereHas('remboursements', function($sq) {
                            $sq->whereColumn('date_paiement', '>', 'credits.date_fin_prevue');
                        });
                    }
                    break;
            }
        });

        return view('livewire.credits.credits-list', [
            'credits' => $query->latest('date_credit')->paginate(12),
            'zones' => Zone::orderBy('nom')->get(),
            'agences' => $user->can('can.level6') ? Agence::all() : []
        ]);
    }
}