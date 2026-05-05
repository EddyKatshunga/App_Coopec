<?php

namespace App\Livewire\Account;

use App\Models\JournalEntry;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class JournalShow extends Component
{
    public JournalEntry $entry;

    public function mount(JournalEntry $entry)
    {
        // On charge toutes les relations nécessaires, y compris les sources potentielles
        $this->entry = $entry->load([
            'lines.account', 
            'agence', 
            'journeeComptable',
            'transaction', 'credit', 'remboursement'
        ]);
    }

    // Helper pour déterminer la source de l'opération
    public function getSourceOperationProperty()
    {
        if ($this->entry->credit) return ['type' => 'Octroi Crédit', 'id' => $this->entry->credit->id];
        if ($this->entry->remboursement) return ['type' => 'Remboursement', 'id' => $this->entry->remboursement->id];
        if ($this->entry->transaction) return ['type' => 'Transaction', 'id' => $this->entry->transaction->id];
        
        return null;
    }

    public function render()
    {
        return view('livewire.account.journal-show');
    }
}