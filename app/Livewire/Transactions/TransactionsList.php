<?php

namespace App\Livewire\Transactions;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\CloturesComptable;
use App\Models\Transaction;
use App\Models\Agence;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TransactionsList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public $search = '';
    public $type = '';
    public $monnaie = '';
    public $date_debut;
    public $date_fin;
    public $journee_ouverte;
    
    // Filtres de périmètre
    public $all_agents = false;
    public $selected_agence_id = null;

    public function mount()
    {
        $user = Auth::user();
        $this->journee_ouverte = $user->journee_ouverte;
        
        // Initialisation des dates sur la journée comptable ou aujourd'hui
        $this->date_debut = $this->journee_ouverte?->date_cloture ?? now()->format('Y-m-d');
        $this->date_fin = $this->date_debut;

        // Si l'utilisateur est restreint à son agence, on la fixe
        if (!$user->can('agence.view.all')) {
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = Transaction::with(['compte.user', 'agence', 'creator']);

        // --- NIVEAU 3 : Vue Globale (Toutes les agences) ---
        if ($user->can('agence.view.all')) {
            $query->when($this->selected_agence_id, fn($q) => $q->where('agence_id', $this->selected_agence_id));
        } 
        // --- NIVEAU 2 & 1 : Restriction à l'agence de l'utilisateur ---
        else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- GESTION DU FILTRE AGENT (NIVEAU 1 vs NIVEAU 2) ---
        // Si l'utilisateur n'a pas la permission de voir toute l'agence 
        // OU s'il a la permission mais qu'il n'a pas coché la case "Toute l'agence"
        if (!$user->can('agence.operations.view') || !$this->all_agents) {
            // Sauf pour le niveau 3 qui voit tout par défaut s'il choisit une agence
            if (!$user->can('agence.view.all')) {
                $query->where('created_by', $user->id);
            }
        }

        // --- FILTRES DYNAMIQUES ---
        $query->when($this->search, function($q) {
            $q->where(function($sub) {
                $sub->whereHas('compte.user', fn($sq) => $sq->where('name', 'like', '%'.$this->search.'%'))
                    ->orWhereHas('compte', fn($sq) => $sq->where('numero_compte', 'like', '%'.$this->search.'%'));
            });
        })
        ->when($this->type, fn($q) => $q->where('type_transaction', $this->type))
        ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_transaction', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_transaction', '<=', $this->date_fin));

        return view('livewire.transactions.transactions-list', [
            'transactions' => $query->oldest('date_transaction')->paginate(100),
            'agences' => $user->can('agence.view.all') ? Agence::all() : []
        ]);
    }
}