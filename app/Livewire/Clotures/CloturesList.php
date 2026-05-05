<?php

namespace App\Livewire\Clotures;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\CloturesComptable;
use App\Models\Agence;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class CloturesList extends Component
{
    use WithPagination;

    public $agenceId = null;
    public $dateDebut;
    public $dateFin;
    public $perPage = 15;

    protected $queryString = ['agenceId', 'dateDebut', 'dateFin', 'perPage'];

    public function mount()
    {
        $user = auth()->user();
        $this->agenceId = $user->agence_id ?? Agence::first()?->id ?? null;
        $this->dateDebut = now()->subDays(30)->format('Y-m-d');
        $this->dateFin = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['agenceId', 'dateDebut', 'dateFin', 'perPage'])) {
            $this->resetPage();
        }
    }

    /**
     * Récupère les statistiques globales sur la période filtrée
     * Utilisation de ClotureStatisticsService pour les agrégats
     */
    public function getStatsProperty()
    {
        $query = CloturesComptable::where('agence_id', $this->agenceId);
        if ($this->dateDebut) $query->whereDate('date_cloture', '>=', $this->dateDebut);
        if ($this->dateFin) $query->whereDate('date_cloture', '<=', $this->dateFin);

        $clotures = $query->get();

        // Agrégation via le modèle (sous-requêtes déjà calculées dans render,
        // mais ici on peut faire directement des sommes sur les collections)
        // Pour éviter des N+1, on charge les relations nécessaires.
        $clotures->load(['transactions', 'credits', 'remboursements']);

        return [
            'nb_jours' => $clotures->count(),
            'total_depot_cdf' => $clotures->sum(fn($c) => $c->transactions->where('type_transaction', 'DEPOT')->where('monnaie', 'CDF')->sum('montant')),
            'total_depot_usd' => $clotures->sum(fn($c) => $c->transactions->where('type_transaction', 'DEPOT')->where('monnaie', 'USD')->sum('montant')),
            'total_retrait_cdf' => $clotures->sum(fn($c) => $c->transactions->where('type_transaction', 'RETRAIT')->where('monnaie', 'CDF')->sum('montant')),
            'total_retrait_usd' => $clotures->sum(fn($c) => $c->transactions->where('type_transaction', 'RETRAIT')->where('monnaie', 'USD')->sum('montant')),
            'total_credit_cdf' => $clotures->sum(fn($c) => $c->credits->where('monnaie', 'CDF')->sum('capital')),
            'total_credit_usd' => $clotures->sum(fn($c) => $c->credits->where('monnaie', 'USD')->sum('capital')),
            'total_remboursement_cdf' => $clotures->sum(fn($c) => $c->remboursements->where('monnaie', 'CDF')->sum('montant')),
            'total_remboursement_usd' => $clotures->sum(fn($c) => $c->remboursements->where('monnaie', 'USD')->sum('montant')),
        ];
    }

    /**
     * Génère le PDF du rapport périodique
     */
    public function genererRapportPeriodique()
    {
        $params = [
            'agence_id' => $this->agenceId,
            'date_debut' => $this->dateDebut,
            'date_fin' => $this->dateFin,
        ];
        return redirect()->route('impressions.rapport.periodique', $params);
    }

    public function render()
    {
        $user = auth()->user();
        $showAllAgences = $user->can('can.level6', CloturesComptable::class);
        $agences = $showAllAgences ? Agence::all() : collect();

        // Requête de base
        $query = CloturesComptable::with(['agence'])
            ->where('agence_id', $this->agenceId);

        // Filtres de dates
        if ($this->dateDebut) {
            $query->whereDate('date_cloture', '>=', $this->dateDebut);
        }
        if ($this->dateFin) {
            $query->whereDate('date_cloture', '<=', $this->dateFin);
        }

        // Sous-requêtes pour les agrégats (remplacement des accesseurs supprimés)
        // Dépôts CDF
        $query->addSelect([
            'total_depot_cdf' => DB::table('transactions')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('transactions.journee_comptable_id', 'clotures_comptables.id')
                ->where('type_transaction', 'DEPOT')
                ->where('monnaie', 'CDF')
        ]);

        // Dépôts USD
        $query->addSelect([
            'total_depot_usd' => DB::table('transactions')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('transactions.journee_comptable_id', 'clotures_comptables.id')
                ->where('type_transaction', 'DEPOT')
                ->where('monnaie', 'USD')
        ]);

        // Retraits CDF
        $query->addSelect([
            'total_retrait_cdf' => DB::table('transactions')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('transactions.journee_comptable_id', 'clotures_comptables.id')
                ->where('type_transaction', 'RETRAIT')
                ->where('monnaie', 'CDF')
        ]);

        // Retraits USD
        $query->addSelect([
            'total_retrait_usd' => DB::table('transactions')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('transactions.journee_comptable_id', 'clotures_comptables.id')
                ->where('type_transaction', 'RETRAIT')
                ->where('monnaie', 'USD')
        ]);

        // Crédits CDF
        $query->addSelect([
            'total_credit_cdf' => DB::table('credits')
                ->selectRaw('COALESCE(SUM(capital), 0)')
                ->whereColumn('credits.journee_comptable_id', 'clotures_comptables.id')
                ->where('monnaie', 'CDF')
        ]);

        // Crédits USD
        $query->addSelect([
            'total_credit_usd' => DB::table('credits')
                ->selectRaw('COALESCE(SUM(capital), 0)')
                ->whereColumn('credits.journee_comptable_id', 'clotures_comptables.id')
                ->where('monnaie', 'USD')
        ]);

        // Remboursements CDF
        $query->addSelect([
            'total_remboursement_cdf' => DB::table('credit_remboursements')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('credit_remboursements.journee_comptable_id', 'clotures_comptables.id')
                ->where('monnaie', 'CDF')
        ]);

        // Remboursements USD
        $query->addSelect([
            'total_remboursement_usd' => DB::table('credit_remboursements')
                ->selectRaw('COALESCE(SUM(montant), 0)')
                ->whereColumn('credit_remboursements.journee_comptable_id', 'clotures_comptables.id')
                ->where('monnaie', 'USD')
        ]);

        $clotures = $query->orderBy('date_cloture', 'desc')->paginate($this->perPage);

        return view('livewire.clotures.clotures-list', [
            'clotures' => $clotures,
            'agences' => $agences,
            'stats' => $this->stats,
            'showAllAgences' => $showAllAgences,
        ]);
    }
}