<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Agence;
use App\Models\Compte;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TransactionListing extends Component
{
    /* =======================
     * FILTRES & ETATS
     * ======================= */

    public int $agence_id;
    public ?int $compte_id = null;

    public string $typeReleve = 'journalier'; // journalier | compte

    public string $date_jour;
    public ?string $date_debut = null;
    public ?string $date_fin = null;

    /* =======================
     * RESULTATS (TABLEAUX PURS)
     * ======================= */

    public array $transactions = [];

    public float $report = 0;
    public float $totalDepot = 0;
    public float $totalRetrait = 0;
    public float $soldeFinal = 0;

    /* =======================
     * INITIALISATION
     * ======================= */

    public function mount()
    {
        $user = Auth::user();

        // Agence par défaut = agence de l’agent connecté
        $this->agence_id = $user->agent->agence_id;

        $this->date_jour   = now()->toDateString();
        $this->date_debut  = now()->startOfMonth()->toDateString();
        $this->date_fin    = now()->toDateString();

        $this->chargerTransactions();
    }

    /* =======================
     * OBSERVATEURS LIVEWIRE
     * ======================= */

    public function updated($property)
    {
        if (in_array($property, [
            'agence_id',
            'compte_id',
            'typeReleve',
            'date_jour',
            'date_debut',
            'date_fin',
        ], true)) {
            $this->chargerTransactions();
        }
    }

    /* =======================
     * ORCHESTRATION METIER
     * ======================= */

    public function chargerTransactions(): void
    {
        $this->resetCalculs();

        if ($this->typeReleve === 'journalier') {
            $this->chargerReleveJournalier();
        } else {
            $this->chargerReleveCompte();
        }
    }

    /* =======================
     * RELEVE JOURNALIER
     * ======================= */

    private function chargerReleveJournalier(): void
    {
        $date = Carbon::parse($this->date_jour);

        /**
         * REPORT = solde fin jour précédent
         */
        $this->report = Transaction::where('agence_id', $this->agence_id)
            ->whereDate('date_transaction', '<', $date)
            ->selectRaw("
                COALESCE(
                    SUM(CASE WHEN type_transaction = 'DEPOT' THEN montant ELSE 0 END) -
                    SUM(CASE WHEN type_transaction = 'RETRAIT' THEN montant ELSE 0 END),
                0)
            ")
            ->value('COALESCE') ?? 0;

        /**
         * TRANSACTIONS DU JOUR
         */
        $collection = Transaction::with([
                'agent_collecteur',
                'compte.membre'
            ])
            ->where('agence_id', $this->agence_id)
            ->whereDate('date_transaction', $date)
            ->orderBy('date_transaction', 'asc')
            ->orderBy('created_at')
            ->get();

        /**
         * TOTAUX
         */
        $this->totalDepot = $collection
            ->where('type_transaction', 'DEPOT')
            ->sum('montant');

        $this->totalRetrait = $collection
            ->where('type_transaction', 'RETRAIT')
            ->sum('montant');

        $this->soldeFinal = $this->report
            + $this->totalDepot
            - $this->totalRetrait;

        /**
         * GROUPEMENT + TRANSFORMATION EN TABLEAUX
         * (CRITIQUE POUR LIVEWIRE)
         */
        $this->transactions = $collection
            ->groupBy([
                'agent_collecteur_id',
                fn ($t) => Carbon::parse($t->date_transaction)->format('Y-m-d'),
            ])
            ->map(function ($dates) {
                return $dates->map(function ($lignes) {
                    return $lignes->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'date_transaction' => $t->date_transaction,
                            'type' => $t->type_transaction,
                            'montant' => (float) $t->montant,
                            'agent' => optional($t->agent_collecteur)->nom,
                            'compte' => $t->compte->numero,
                        ];
                    })->values();
                })->values();
            })
            ->toArray();
    }

    /* =======================
     * RELEVE D’UN COMPTE
     * ======================= */

    private function chargerReleveCompte(): void
    {
        if (!$this->compte_id || !$this->date_debut || !$this->date_fin) {
            $this->transactions = [];
            return;
        }

        $collection = Transaction::with([
                'agent_collecteur',
                'compte.membre'
            ])
            ->where('compte_id', $this->compte_id)
            ->whereBetween('date_transaction', [
                $this->date_debut,
                $this->date_fin,
            ])
            ->orderBy('date_transaction')
            ->orderBy('created_at')
            ->get();

        $this->transactions = $collection
            ->groupBy([
                'agent_collecteur_id',
                fn ($t) => Carbon::parse($t->date_transaction)->format('Y-m-d'),
            ])
            ->map(function ($dates) {
                return $dates->map(function ($lignes) {
                    return $lignes->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'date_transaction' => $t->date_transaction,
                            'type' => $t->type_transaction,
                            'montant' => (float) $t->montant,
                            'agent' => optional($t->agent_collecteur)->nom,
                            'compte' => $t->compte->numero,
                        ];
                    })->values();
                })->values();
            })
            ->toArray();
    }

    /* =======================
     * RESET
     * ======================= */

    private function resetCalculs(): void
    {
        $this->transactions = [];
        $this->report = 0;
        $this->totalDepot = 0;
        $this->totalRetrait = 0;
        $this->soldeFinal = 0;
    }

    /* =======================
     * RENDER
     * ======================= */

    public function render()
    {
        return view('livewire.transactions.transaction-listing', [
            'agences' => Auth::user()->can('changer agence transactions')
                ? Agence::orderBy('nom')->get()
                : collect(),

            'comptes' => Compte::orderBy('numero')->get(),
        ]);
    }
}
