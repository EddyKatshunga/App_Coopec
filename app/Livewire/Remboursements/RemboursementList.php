<?php

namespace App\Livewire\Remboursements;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\CreditRemboursement;
use App\Models\Credit;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RemboursementList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public ?Credit $credit = null; 
    public $search = '';
    public $date_debut;
    public $date_fin;
    public $monnaie = '';

    // Filtres de périmètre
    public $all_agents = false;
    public $selected_agence_id = null;

    public function mount()
    {
        $user = Auth::user();
        $this->date_debut = auth()->user()->journee_ouverte?->date_cloture->format('Y-m-d');
        $this->date_fin = now()->format('Y-m-d');

        if (!$user->can('can.level6')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = CreditRemboursement::with(['credit.user', 'agence', 'creator']);

        // --- FILTRE PAR CRÉDIT SPÉCIFIQUE (Si passé en paramètre) ---
        if ($this->credit) {
            $query->where('credit_id', $this->credit->id);
        }

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

        // --- FILTRES DYNAMIQUES ---
        $query->when($this->search, function($q) {
            $q->where(function($sub) {
                $sub->whereHas('credit', fn($sq) => $sq->where('reference', 'like', '%'.$this->search.'%'))
                    ->orWhereHas('credit.user', fn($sq) => $sq->where('name', 'like', '%'.$this->search.'%'));
            });
        })
        ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_paiement', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_paiement', '<=', $this->date_fin));

        return view('livewire.remboursements.remboursement-list', [
            'remboursements' => $query->latest('date_paiement')->paginate(100),
            'agences' => $user->can('can.level6') ? Agence::all() : []
        ]);
    }
}