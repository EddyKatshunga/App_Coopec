<?php

namespace App\Livewire\Remboursements;

use App\Livewire\Traits\CanDeleteAccountingRecords;
use App\Models\CreditRemboursement;
use App\Models\Credit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RemboursementList extends Component
{
    use WithPagination, CanDeleteAccountingRecords;

    public ?Credit $credit = null; // Optionnel : filtrer par crédit
    public $search = '';

    public function render()
    {
        $query = CreditRemboursement::with(['credit.membre', 'agent', 'zone'])
            ->latest('date_paiement');

        if ($this->credit) {
            $query->where('credit_id', $this->credit->id);
        }

        return view('livewire.remboursements.remboursement-list', [
            'remboursements' => $query->paginate(10)
        ]);
    }
}
