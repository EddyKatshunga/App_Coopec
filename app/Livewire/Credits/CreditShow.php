<?php

namespace App\Livewire\Credits;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use Livewire\Component;
use App\Models\Credit;
use App\Services\CreditService;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreditShow extends Component
{
    use CanDeleteAccountingRecords;
    
    public Credit $credit;
    
    // État du Modal (remplace Alpine)
    public bool $showClotureModal = false;

    // Propriétés du formulaire
    public string $noteCloture = '';
    public bool $confirmCloture = false;

    // Propriétés de situation financière
    public float $penaliteCourante = 0;
    public int $joursRetard = 0;
    public float $resteDu = 0;
    public float $resteGlobal = 0;

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
        $this->credit->load('remboursements');
        
        // Utilisation des attributs du modèle au lieu de getSituationActuelle()
        $this->penaliteCourante = $this->credit->penalites_courantes;
        $this->joursRetard = $this->credit->jours_retards;
        $this->resteDu = $this->credit->reste_du;
        $this->resteGlobal = $this->credit->reste_global;
    }

    // Ouvre/Ferme le modal et réinitialise les champs
    public function toggleClotureModal()
    {
        $this->showClotureModal = !$this->showClotureModal;
        if (!$this->showClotureModal) {
            $this->reset(['noteCloture', 'confirmCloture']);
            $this->resetValidation();
        }
    }

    public function validerClotureForcee(CreditService $service)
    {
        $this->validate([
            'noteCloture' => 'required|min:5',
            'confirmCloture' => 'accepted'
        ], [
            'noteCloture.required' => 'Le motif est obligatoire.',
            'confirmCloture.accepted' => 'Veuillez confirmer l\'opération.'
        ]);

        $service->forcerCloture($this->credit, $this->noteCloture);

        // Mise à jour de l'interface
        $this->showClotureModal = false;
        $this->credit->refresh();
        $this->rafraichirEtat();

        session()->flash('success', 'Le crédit a été clôturé avec succès.');
    }

    public function render()
    {
        // Tri des remboursements par date de paiement (ASC)
        $remboursements = $this->credit->remboursements()
            ->orderBy('date_paiement', 'asc')
            ->get();
            
        // Calcul du capital restant dynamique pour chaque remboursement
        $resteApres = $this->credit->total;
        foreach ($remboursements as $remb) {
            $resteApres -= ($remb->montant_capital_payee + $remb->montant_interet_payee);
            $remb->reste_du_apres = max(0, $resteApres);
        }

        return view('livewire.credits.credit-show', [
            'remboursements' => $remboursements,
        ]);
    }
}