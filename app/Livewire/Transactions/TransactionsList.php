<?php

namespace App\Livewire\Transactions;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\CloturesComptable;
use App\Models\Transaction;
use App\Models\Agence;
use App\Models\User;
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

    // Filtres
    public $selected_agence_id = null;   // obligatoire pour can.level6
    public $selected_creator_id = null;  // optionnel, pour can.level4

    public function mount()
    {
        $user = Auth::user();
        $this->journee_ouverte = $user->journee_ouverte;

        // Dates par défaut
        $derniereCloture = CloturesComptable::where('agence_id', $user->agence_id)
            ->latest('date_cloture')->first();
        $dateParDefaut = $derniereCloture
            ? $derniereCloture->date_cloture->format('Y-m-d')
            : now()->format('Y-m-d');

        $this->date_debut = $dateParDefaut;
        $this->date_fin = $dateParDefaut;

        // Agence obligatoire
        if ($user->can('can.level6')) {
            // Pour niveau 6 : on présélectionne l'agence de l'utilisateur si elle existe,
            // sinon la première agence disponible.
            $this->selected_agence_id = $user->agence_id ?? Agence::first()->id;
        } else {
            // Les autres n'ont pas le choix, on fixe l'agence.
            $this->selected_agence_id = $user->agence_id;
        }
    }

    public function updating() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = Transaction::with(['compte.user', 'agence', 'creator']);

        // --- Périmètre d'agence ---
        if ($user->can('can.level6')) {
            // Filtre obligatoire : $selected_agence_id garanti non null
            $query->where('agence_id', $this->selected_agence_id);
        } else {
            $query->where('agence_id', $user->agence_id);
        }

        // --- Filtre par agent créateur ---
        if (!$user->can('can.level4')) {
            // Niveau 1-3 : ne voient que leurs propres transactions
            $query->where('created_by', $user->id);
        } else {
            // Niveau 4+ : filtrage optionnel par créateur
            if ($this->selected_creator_id) {
                $query->where('created_by', $this->selected_creator_id);
            }
            // sinon toutes les transactions de l’agence
        }

        // --- Autres filtres dynamiques ---
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

        // --- Liste des agents créateurs disponibles selon les filtres actuels (sauf agent) ---
        $availableCreators = collect();
        if ($user->can('can.level4')) {
            $baseQuery = Transaction::query()
                ->when($this->type, fn($q) => $q->where('type_transaction', $this->type))
                ->when($this->monnaie, fn($q) => $q->where('monnaie', $this->monnaie))
                ->when($this->date_debut, fn($q) => $q->whereDate('date_transaction', '>=', $this->date_debut))
                ->when($this->date_fin, fn($q) => $q->whereDate('date_transaction', '<=', $this->date_fin));

            if ($user->can('can.level6')) {
                $baseQuery->where('agence_id', $this->selected_agence_id);
            } else {
                $baseQuery->where('agence_id', $user->agence_id);
            }

            $creatorIds = $baseQuery->distinct()->pluck('created_by');
            $availableCreators = User::whereIn('id', $creatorIds)->orderBy('name')->get();
        }

        // --- Liste des monnaies disponibles ---
        $monnaies = Transaction::select('monnaie')->distinct()->pluck('monnaie');

        return view('livewire.transactions.transactions-list', [
            'transactions'       => $query->oldest('date_transaction')->paginate(100),
            'agences'            => $user->can('can.level6') ? Agence::all() : [],
            'availableCreators'  => $availableCreators,
            'monnaies'           => $monnaies,
        ]);
    }
}