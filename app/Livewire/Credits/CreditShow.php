<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use App\Models\Credit;
use App\Services\CreditCalculatorService;
use Carbon\Carbon;

class CreditShow extends Component
{
    public Credit $credit;

    public float $penaliteCourante = 0;
    public int $joursRetard = 0;
    public float $totalRembourse = 0;
    public float $resteDu = 0;

    protected CreditCalculatorService $calculator;

    protected $listeners = [
        'remboursementAdded' => 'rafraichirEtat',
    ];

    public function mount(Credit $credit) 
    {
        $this->calculator = new CreditCalculatorService();
        $this->credit = $credit;

        $this->rafraichirEtat();
    }

    public function rafraichirEtat(): void
    {
        $today = now();

        // Pénalités en direct
        $this->penaliteCourante = $this->calculator
            ->calculerPenalite($this->credit, $today);

        // Jour de retard après délai de grâce
        $dateDebutPenalite = $this->credit->date_fin_prevue->copy()->addDays(10);
        $this->joursRetard = $today->gt($dateDebutPenalite)
            ? $dateDebutPenalite->diffInDays($today)
            : 0;

        // Total remboursé
        $this->totalRembourse = $this->credit
            ->remboursements()
            ->sum('montant');

        // Reste dû
        $this->resteDu = max(
            0,
            ($this->credit->capital + $this->credit->interet)
            + $this->penaliteCourante
            - $this->totalRembourse
        );
    }

    // S'assurer que le service est toujours initialisé
    public function boot(): void
    {
        if (!isset($this->calculator)) {
            $this->calculator = new CreditCalculatorService();
        }
    }

    public function render()
    {
        return view('livewire.credits.credit-show', [
            'remboursements' => $this->credit
                ->remboursements()
                ->orderBy('date_paiement')
                ->get(),
        ])->layout('layouts.app');
    }
}
