<?php

namespace App\Livewire\Transactions;

use App\Models\CloturesComptable;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TransactionsList extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $monnaie = '';
    public $date_debut;
    public $date_fin;
    
    // Filtre administrateur/superviseur
    public $all_agents = false;

    public int $agenceId;
    public ?CloturesComptable $journee_ouverte;

    public function mount()
    {
        $this->agenceId = auth()->user()->agence_id;
        $this->journee_ouverte = auth()->user()->journee_ouverte;
        // Par défaut, on filtre sur la journée en cours
        $this->date_debut = $this->journee_ouverte?->date_cloture ?? now()->format('Y-m-d');
    }

    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = Transaction::with(['compte.user', 'agence'])
            ->where('agence_id', $this->agenceId);

        // Filtrage par agent (sauf si coché "tous les agents" et que l'utilisateur a les droits)
        if (!$this->all_agents) {
            $query->where('agent_collecteur_id', auth()->user()->agent->id);
        }

        // Filtres Dynamiques
        $query->when($this->search, function($q) {
            $q->whereHas('compte.user', fn($sq) => $sq->where('name', 'like', '%'.$this->search.'%'))
              ->orWhereHas('compte', fn($sq) => $sq->where('numero_compte', 'like', '%'.$this->search.'%'));
        })
        ->when($this->type, fn($q) => $q->where('type_transaction', $this->type))
        ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
        ->when($this->date_debut, fn($q) => $q->whereDate('date_transaction', '>=', $this->date_debut))
        ->when($this->date_fin, fn($q) => $q->whereDate('date_transaction', '<=', $this->date_fin));

        return view('livewire.transactions.transactions-list', [
            'transactions' => $query->orderBy('date_transaction', 'asc')->paginate(100),
        ]);
    }
}