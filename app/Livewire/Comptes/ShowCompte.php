<?php

namespace App\Livewire\Comptes;

use Livewire\Component;
use App\Models\Compte;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShowCompte extends Component
{
    public Compte $compte;
    public $dateDebut;
    public $dateFin;
    public $monnaie = '';

    // Query string pour garder les filtres dans l'URL (le petit plus pour le rafraîchissement)
    protected $queryString = [
        'dateDebut' => ['except' => ''],
        'dateFin' => ['except' => ''],
        'monnaie' => ['except' => ''],
        'typeAction' => ['except' => ''],
    ];

    public function mount(Compte $compte)
    {
        $this->authorize('view', $compte);
        $this->compte = $compte->load(['membre']);
        
        $this->dateDebut = $this->dateDebut ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateFin = $this->dateFin ?? Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        // On construit la base de la requête
        $query = $this->compte->transactions()
            ->whereBetween('date_transaction', [$this->dateDebut, $this->dateFin])
            ->with(['agent_collecteur.user', 'creator'])
            ->oldest('date_transaction');

        if ($this->monnaie) $query->where('monnaie', $this->monnaie);

        $transactions = $query->get();

        // Statistiques filtrées (Le petit plus : les compteurs réagissent aux filtres !)
        $stats = [
            'depot_cdf' => $transactions->where('monnaie', 'CDF')->where('type_transaction', 'DEPOT')->sum('montant'),
            'retrait_cdf' => $transactions->where('monnaie', 'CDF')->where('type_transaction', 'RETRAIT')->sum('montant'),
            'depot_usd' => $transactions->where('monnaie', 'USD')->where('type_transaction', 'DEPOT')->sum('montant'),
            'retrait_usd' => $transactions->where('monnaie', 'USD')->where('type_transaction', 'RETRAIT')->sum('montant'),
        ];

        return view('livewire.comptes.show-compte', [
            'transactions' => $transactions,
            'stats' => $stats
        ]);
    }
}