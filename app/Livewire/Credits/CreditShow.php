<?php

namespace App\Livewire\Credits;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use Livewire\Component;
use App\Models\Credit;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreditShow extends Component
{
    use CanDeleteAccountingRecords;
    
    public Credit $credit;

    // On garde ces propriétés pour le binding Livewire si nécessaire, 
    // mais on les alimente via le modèle.
    public float $penaliteCourante = 0;
    public int $joursRetard = 0;
    public float $resteDu = 0;

    protected $listeners = [
        'remboursementAdded' => 'rafraichirEtat',
    ];

    public function mount(Credit $credit) 
    {
        $this->credit = $credit;
        $this->rafraichirEtat();
    }

    public function rafraichirEtat(): void
    {
        // On force le rafraîchissement des relations pour inclure le nouveau remboursement
        $this->credit->load('remboursements');
        
        // On utilise la méthode de calcul centralisée du modèle
        $situation = $this->credit->getSituationActuelle();

        $this->penaliteCourante = $situation['penalites_courantes'];
        $this->joursRetard = $situation['jours_retard_courants'];
        
        // Le Reste dû affiché est le "Total à payer" (Base + pénalités du jour)
        $this->resteDu = $situation['total_a_payer'];
    }

    public function render()
    {
        return view('livewire.credits.credit-show', [
            'remboursements' => $this->credit->remboursements()
                ->orderBy('date_paiement', 'asc')
                ->get(),
        ]);
    }
}