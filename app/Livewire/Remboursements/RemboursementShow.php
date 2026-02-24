<?php

namespace App\Livewire\Remboursements;

use App\Models\CreditRemboursement;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RemboursementShow extends Component
{
    public CreditRemboursement $remboursement;

    public function mount(CreditRemboursement $remboursement)
    {
        $this->remboursement = $remboursement->load(['credit.membre', 'agent', 'agence']);
    }

    public function render()
    {
        return view('livewire.remboursements.remboursement-show');
    }
}