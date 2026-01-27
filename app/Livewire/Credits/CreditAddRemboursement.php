<?php

namespace App\Livewire\Credits;

use Livewire\Component;
use App\Models\Credit;
use App\Services\CreditRemboursementService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CreditAddRemboursement extends Component
{
    public Credit $credit;

    // Formulaire
    public $date_paiement;
    public $montant;
    public $mode_paiement = 'cash';

    public $showModal = false;

    protected $rules = [
        'date_paiement' => 'required|date',
        'montant' => 'required|numeric|min:0.01',
        'mode_paiement' => 'required|in:cash,mpesa,airtel,banque',
    ];

    protected $messages = [
        'montant.min' => 'Le montant doit être supérieur à zéro.',
    ];

    protected CreditRemboursementService $service;

    public function mount(Credit $credit, CreditRemboursementService $service)
    {
        $this->credit = $credit;
        $this->service = $service;
        $this->date_paiement = now()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->montant = null;
        $this->mode_paiement = 'cash';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate();

        $this->service->enregistrer($this->credit, [
            'date_paiement' => $this->date_paiement,
            'montant' => $this->montant,
            'agent_id' => Auth::id(),
            'mode_paiement' => $this->mode_paiement,
        ]);

        session()->flash('success', 'Remboursement enregistré avec succès.');

        $this->closeModal();

        // rafraîchir la vue parent si nécessaire
        $this->emit('remboursementAdded');
    }

    public function render()
    {
        return view('livewire.credits.credit-add-remboursement');
    }
}
